<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Domains\Modules\Repositories\ModuleRepositoryInterface;
use App\Exceptions\ApiException;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class ModuleApiService
{
    public function __construct(
        private ModuleRepositoryInterface $moduleRepository
    ) {
    }

    /**
     * Get all modules with pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllModules(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->moduleRepository->list(
            with: ['units'],
            filters: $filters,
            pagination: ['per_page' => $perPage]
        );
    }

    /**
     * Get specific Module.
     *
     * @param int $moduleId
     * @return Model|Module
     * @throws ApiException
     */
    public function getModule(int $moduleId): Model|Module
    {
        $module = $this->moduleRepository->getOne(
            filters: ['id' => $moduleId],
            with: ['units']
        );
        
        if (!$module) {
            throw new ApiException(message: 'Módulo no encontrado', code: 404);
        }
        
        return $module;
    }

    /**
     * Create Module
     *
     * @param array $data
     * @return Module
     * @throws ApiException
     * @throws Throwable
     */
    public function createModule(array $data): Module
    {
        // Get authenticated user for logging/permissions
        /** @var User $currentUser */
        $currentUser = Auth::user();

        // Verificar permisos (solo teacher puede crear módulos)
        if (!$currentUser->isTeacher()) {
            throw new ApiException(message: 'No tiene permisos para crear módulos', code: 403);
        }

        try {
            DB::beginTransaction();

            $moduleData = [
                'name' => $data['name'],
            ];

            $module = $this->moduleRepository->save($moduleData);
            
            if (!$module) {
                throw new ApiException(message: 'Error al crear módulo', code: 500);
            }

            DB::commit();

            // Refresh the module model with relations
            return $this->moduleRepository->getOne(
                filters: ['id' => $module->id],
                with: ['units']
            );

        } catch (ApiException $e) {
            DB::rollback();
            throw $e;
        } catch (Throwable $e) {
            DB::rollback();
            throw new ApiException(
                message: 'Error al crear módulo: ' . $e->getMessage(),
                code: 500
            );
        }
    }
    
    /**
     * Update Module
     *
     * @param array $data
     * @param int $moduleId
     * @return Module
     * @throws ApiException
     * @throws Throwable
     */
    public function updateModule(array $data, int $moduleId): Module
    {
        // Get authenticated user for logging/permissions
        /** @var User $currentUser */
        $currentUser = Auth::user();

        // Verificar permisos (solo admin puede modificar módulos)
        if (!$currentUser->isTeacher()) {
            throw new ApiException(message: 'No tiene permisos para modificar módulos', code: 403);
        }

        $module = $this->moduleRepository->getOne(filters: ['id' => $moduleId]);
        
        if (!$module) {
            throw new ApiException(message: 'Módulo no encontrado', code: 404);
        }
        
        try {
            DB::beginTransaction();

            $moduleData = [
                'name' => $data['name'] ?? $module->name,
            ];

            $module = $this->moduleRepository->save($moduleData, $module);
            
            if (!$module) {
                throw new ApiException(message: 'Error al actualizar módulo', code: 500);
            }

            DB::commit();

            // Refresh the module model with relations
            return $this->moduleRepository->getOne(
                filters: ['id' => $module->id],
                with: ['units']
            );

        } catch (ApiException $e) {
            DB::rollback();
            throw $e;
        } catch (Throwable $e) {
            DB::rollback();
            throw new ApiException(
                message: 'Error al actualizar módulo: ' . $e->getMessage(),
                code: 500
            );
        }
    }
    
    /**
     * Delete a module.
     *
     * @param int $moduleId
     * @return bool
     * @throws ApiException
     * @throws Throwable
     */
    public function deleteModule(int $moduleId): bool
    {
        // Get authenticated user for permissions
        /** @var User $currentUser */
        $currentUser = Auth::user();
        
        // Verificar permisos (solo admin puede eliminar módulos)
        if (!$currentUser->isTeacher()) {
            throw new ApiException(message: 'No tiene permisos para eliminar módulos', code: 403);
        }
        
        $module = $this->moduleRepository->getOne(filters: ['id' => $moduleId]);
        
        if (!$module) {
            throw new ApiException(message: 'Módulo no encontrado', code: 404);
        }
        
        try {
            DB::beginTransaction();
            
            // Verificar si tiene unidades o evaluaciones
            if ($module->units()->count() > 0) {
                throw new ApiException(
                    message: 'No se puede eliminar el módulo porque tiene unidades asociadas',
                    code: 409
                );
            }
            
            if ($module->evaluations()->count() > 0) {
                throw new ApiException(
                    message: 'No se puede eliminar el módulo porque tiene evaluaciones asociadas',
                    code: 409
                );
            }
            
            // Eliminar módulo
            $result = $this->moduleRepository->delete($module);
            
            if (!$result) {
                throw new ApiException(message: 'Error al eliminar módulo', code: 500);
            }
            
            DB::commit();
            return true;
        } catch (Throwable $e) {
            DB::rollback();
            throw new ApiException(
                message: 'Error al eliminar módulo: ' . $e->getMessage(),
                code: $e instanceof ApiException ? $e->getCode() : 500
            );
        }
    }
    
    /**
     * Get units for a specific module.
     *
     * @param int $moduleId
     * @return array
     * @throws ApiException
     */
    public function getModuleUnits(int $moduleId): array
    {
        $module = $this->moduleRepository->getOne(
            filters: ['id' => $moduleId],
            with: ['units.teacher']
        );
        
        if (!$module) {
            throw new ApiException(message: 'Módulo no encontrado', code: 404);
        }
        
        return $module->units->map(function ($unit) {
            return [
                'id' => $unit->id,
                'name' => $unit->name,
                'teacher' => $unit->teacher ? [
                    'id' => $unit->teacher->id,
                    'name' => $unit->teacher->first_name . ' ' . $unit->teacher->last_name
                ] : null
            ];
        })->toArray();
    }
}
