<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Domains\Units\Repositories\UnitRepositoryInterface;
use App\Exceptions\ApiException;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UnitApiService
{
    public function __construct(
        private UnitRepositoryInterface $unitRepository
    ) {
    }

    /**
     * Get all units with pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllUnits(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->unitRepository->list(
            with: ['module', 'teacher'],
            filters: $filters,
            pagination: ['per_page' => $perPage]
        );
    }

    /**
     * Get specific Unit.
     *
     * @param int $unitId
     * @return Model|Unit
     * @throws ApiException
     */
    public function getUnit(int $unitId): Model|Unit
    {
        $unit = $this->unitRepository->getOne(
            filters: ['id' => $unitId],
            with: ['module', 'teacher']
        );

        if (!$unit) {
            throw new ApiException(message: 'Unidad no encontrada', code: 404);
        }

        return $unit;
    }

    /**
     * Create Unit
     *
     * @param array $data
     * @return Unit
     * @throws ApiException
     * @throws Throwable
     */
    public function createUnit(array $data): Unit
    {
        // Get authenticated user for logging/permissions
        /** @var User $currentUser */
        $currentUser = Auth::user();

        // Verificar permisos (admin o profesor pueden crear unidades)
        if (!$currentUser->isTeacher()) {
            throw new ApiException(message: 'No tiene permisos para crear unidades', code: 403);
        }

        try {
            DB::beginTransaction();

            $unitData = [
                'title' => $data['title'],
                'module_id' => $data['module_id'],
                'teacher_id' => $data['teacher_id'] ??
                    ($currentUser->isTeacher() && $currentUser->teacher ? $currentUser->teacher->id : null),
            ];

            // Verificar que la unidad tenga un profesor asignado
            if (empty($unitData['teacher_id'])) {
                throw new ApiException(message: 'La unidad debe tener un profesor asignado', code: 400);
            }

            $unit = $this->unitRepository->save($unitData);

            if (!$unit) {
                throw new ApiException(message: 'Error al crear unidad', code: 500);
            }

            DB::commit();

            // Refresh the unit model with relations
            return $this->unitRepository->getOne(
                filters: ['id' => $unit->id],
                with: ['module', 'teacher']
            );

        } catch (ApiException $e) {
            DB::rollback();
            throw $e;
        } catch (Throwable $e) {
            DB::rollback();
            throw new ApiException(
                message: 'Error al crear unidad: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    /**
     * Update Unit
     *
     * @param array $data
     * @param int $unitId
     * @return Unit
     * @throws ApiException
     * @throws Throwable
     */
    public function updateUnit(array $data, int $unitId): Unit
    {
        // Get authenticated user for logging/permissions
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $unit = $this->unitRepository->getOne(
            filters: ['id' => $unitId],
            with: ['teacher']
        );

        if (!$unit) {
            throw new ApiException(message: 'Unidad no encontrada', code: 404);
        }

        // Verificar permisos (el profesor asignado a la unidad)
        $isAssignedTeacher = $currentUser->isTeacher() &&
            $currentUser->teacher &&
            $unit->teacher_id === $currentUser->teacher->id;

        if (!$isAssignedTeacher) {
            throw new ApiException(
                message: 'No tiene permisos para modificar esta unidad',
                code: 403
            );
        }

        try {
            DB::beginTransaction();

            $unitData = [];

            // Teacher can only modify the title
            $unitData = [
                'title' => $data['title'] ?? $unit->name,
            ];

            $unit = $this->unitRepository->save($unitData, $unit);

            if (!$unit) {
                throw new ApiException(message: 'Error al actualizar unidad', code: 500);
            }

            DB::commit();

            // Refresh the unit model with relations
            return $this->unitRepository->getOne(
                filters: ['id' => $unit->id],
                with: ['module', 'teacher']
            );

        } catch (ApiException $e) {
            DB::rollback();
            throw $e;
        } catch (Throwable $e) {
            DB::rollback();
            throw new ApiException(
                message: 'Error al actualizar unidad: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    /**
     * Delete a unit.
     *
     * @param int $unitId
     * @return bool
     * @throws ApiException
     * @throws Throwable
     */
    public function deleteUnit(int $unitId): bool
    {
        // Get authenticated user for permissions
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $unit = $this->unitRepository->getOne(
            filters: ['id' => $unitId],
            with: ['teacher', 'evaluations']
        );

        if (!$unit) {
            throw new ApiException(message: 'Unidad no encontrada', code: 404);
        }

        // Verificar permisos (profesor asignado)
        $isAssignedTeacher = $currentUser->isTeacher() &&
            $currentUser->teacher &&
            $unit->teacher_id === $currentUser->teacher->id;

        if (!$isAssignedTeacher) {
            throw new ApiException(
                message: 'No tiene permisos para eliminar esta unidad',
                code: 403
            );
        }

        try {
            DB::beginTransaction();

            // Verificar si tiene evaluaciones
            if ($unit->evaluations()->count() > 0) {
                throw new ApiException(
                    message: 'No se puede eliminar la unidad porque tiene evaluaciones asociadas',
                    code: 409
                );
            }

            // Delete the unit
            $result = $this->unitRepository->delete($unit);

            if (!$result) {
                throw new ApiException(message: 'Error al eliminar unidad', code: 500);
            }

            DB::commit();
            return true;
        } catch (Throwable $e) {
            DB::rollback();
            throw new ApiException(
                message: 'Error al eliminar unidad: ' . $e->getMessage(),
                code: $e instanceof ApiException ? $e->getCode() : 500
            );
        }
    }

    /**
     * Get evaluations for a specific unit.
     *
     * @param int $unitId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getUnitEvaluations(int $unitId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $unit = $this->unitRepository->getOne(
            filters: ['id' => $unitId]
        );

        if (!$unit) {
            throw new ApiException(message: 'Unidad no encontrada', code: 404);
        }

        $filters['unit_id'] = $unitId;

        return $unit->evaluations()
            ->with(['student', 'teacher'])
            ->when(isset($filters['student_id']), function ($query) use ($filters) {
                $query->where('student_id', $filters['student_id']);
            })
            ->when(isset($filters['teacher_id']), function ($query) use ($filters) {
                $query->where('teacher_id', $filters['teacher_id']);
            })
            ->paginate($perPage);
    }
}
