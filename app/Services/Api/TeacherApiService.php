<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Domains\Teachers\Repositories\TeacherRepositoryInterface;
use App\Exceptions\ApiException;
use App\Models\Teacher;
use App\Models\User;
use App\Enums\UserRoleEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

final readonly class TeacherApiService
{
    public function __construct(
        private TeacherRepositoryInterface $teacherRepository
    ) {
    }

    /**
     * Get all teachers with pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllTeachers(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        // Utilizando list() del BaseRepository para obtener datos paginados
        return $this->teacherRepository->list(
            with: ['user'],
            filters: $filters,
            pagination: ['per_page' => $perPage]
        );
    }

    /**
     * Get specific Teacher in base of specific attribute.
     *
     * @param int|null $teacherId
     * @return Model|Teacher
     * @throws ApiException
     */
    public function getTeacher(?int $teacherId = null): Model|Teacher
    {
        if (!$teacherId) {
            // Get authenticated user
            /** @var User $currentUser */
            $currentUser = Auth::user();

            // Get teacher associated with current user
            $teacher = $this->teacherRepository->getOne(
                filters: ['user_id' => $currentUser->id],
                with: ['user']
            );

            // Check if teacher exists
            if (!$teacher) {
                throw new ApiException(message: 'El usuario actual no tiene un perfil de profesor', code: 404);
            }

            return $teacher;
        }

        $teacher = $this->teacherRepository->getOne(
            filters: ['id' => $teacherId],
            with: ['user']
        );

        // Check if teacher exists
        if (!$teacher) {
            throw new ApiException(message: 'Profesor no encontrado', code: 404);
        }

        return $teacher;
    }

    /**
     * Update or Create Teacher information
     *
     * @param array $data
     * @param int|null $teacherId Para actualizar un profesor específico (opcional)
     * @return Teacher
     * @throws ApiException
     * @throws Throwable
     */
    public function updateOrCreateTeacher(array $data, ?int $teacherId = null): Teacher
    {
        // Get authenticated user
        /** @var User $currentUser */
        $currentUser = Auth::user();

        // Determinar qué profesor actualizar
        $teacher = null;
        if ($teacherId) {
            $teacher = $this->teacherRepository->getOne(filters: ['id' => $teacherId]);
            if (!$teacher) {
                throw new ApiException(message: 'Profesor no encontrado', code: 404);
            }
        } else {
            // Get teacher associated with current user if exists
            $teacher = $this->teacherRepository->getOne(filters: ['user_id' => $currentUser->id]);
        }

        try {
            DB::beginTransaction();

            if (!$teacher) {
                // Create new teacher
                $teacherData = [
                    'user_id' => $currentUser->id,
                    'first_name' => $data['first_name'] ?? $currentUser->name,
                    'last_name' => $data['last_name'] ?? '',
                ];

                $teacher = $this->teacherRepository->save($teacherData);

                if (!$teacher) {
                    throw new ApiException(message: 'Error al crear profesor', code: 500);
                }

                // Actualizar el rol del usuario a ADMIN
                $currentUser->update(['role' => UserRoleEnum::ADMIN->value]);
            } else {
                // Update existing teacher
                $teacherData = [
                    'first_name' => $data['first_name'] ?? $teacher->first_name,
                    'last_name' => $data['last_name'] ?? $teacher->last_name
                ];

                $teacher = $this->teacherRepository->save($teacherData, $teacher);

                if (!$teacher) {
                    throw new ApiException(message: 'Error al actualizar profesor', code: 500);
                }
            }

            // Update user info if provided
            if ($teacherId === null || $teacher->user_id === $currentUser->id) {
                $userUpdateData = [];

                if (isset($data['name'])) {
                    $userUpdateData['name'] = $data['name'];
                }

                if (isset($data['email']) && $data['email'] !== $currentUser->email) {
                    $userUpdateData['email'] = $data['email'];
                    // Reset email verification if email changes
                    $userUpdateData['email_verified_at'] = null;
                }

                if (isset($data['new_password'])) {
                    $userUpdateData['password'] = Hash::make($data['new_password']);
                }

                if (!empty($userUpdateData)) {
                    // Aquí deberíamos usar un UserRepository, pero por simplicidad actualizamos directamente
                    $currentUser->update($userUpdateData);
                }
            }

            DB::commit();

            // Refresh the teacher model with the user relation
            return $this->teacherRepository->getOne(
                filters: ['id' => $teacher->id],
                with: ['user']
            );

        } catch (Throwable $e) {
            DB::rollback();
            throw new ApiException(
                message: 'Error al actualizar/crear profesor: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    /**
     * Delete a teacher.
     *
     * @param int $teacherId
     * @return bool
     * @throws ApiException
     * @throws Throwable
     */
    public function deleteTeacher(int $teacherId): bool
    {
        $teacher = $this->teacherRepository->getOne(filters: ['id' => $teacherId]);

        if (!$teacher) {
            throw new ApiException(message: 'Profesor no encontrado', code: 404);
        }

        try {
            DB::beginTransaction();

            // Eliminar profesor utilizando el método delete del repositorio
            $result = $this->teacherRepository->delete($teacher);

            if (!$result) {
                throw new ApiException(message: 'Error al eliminar profesor', code: 500);
            }

            DB::commit();
            return true;
        } catch (Throwable $e) {
            DB::rollback();
            throw new ApiException(
                message: 'Error al eliminar profesor: ' . $e->getMessage(),
                code: 500
            );
        }
    }
}
