<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Domains\Students\Repositories\StudentRepositoryInterface;
use App\Exceptions\ApiException;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

final readonly class StudentApiService
{
    public function __construct(
        private StudentRepositoryInterface $studentRepository
    ) {
    }

    /**
     * Get all students with pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllStudents(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        // Utilizando list() del BaseRepository para obtener datos paginados
        return $this->studentRepository->list(
            with: ['user'],
            filters: $filters,
            pagination: ['per_page' => $perPage]
        );
    }

    /**
     * Get specific Student in base of specific attribute.
     *
     * @param int|null $studentId
     * @return Model|Student
     * @throws ApiException
     */
    public function getStudent(?int $studentId = null): Model|Student
    {
        if (!$studentId) {
            // Get authenticated user
            /** @var User $currentUser */
            $currentUser = Auth::user();
            
            // Get student associated with current user
            $student = $this->studentRepository->getOne(
                filters: ['user_id' => $currentUser->id],
                with: ['user']
            );
            
            // Check if student exists
            if (!$student) {
                throw new ApiException(message: 'El usuario actual no tiene un perfil de estudiante', code: 404);
            }
            
            return $student;
        }
        
        $student = $this->studentRepository->getOne(
            filters: ['id' => $studentId],
            with: ['user']
        );
        
        // Check if student exists
        if (!$student) {
            throw new ApiException(message: 'Estudiante no encontrado', code: 404);
        }
        
        return $student;
    }

    /**
     * Update or Create Student information
     *
     * @param array $data
     * @param int|null $studentId Para actualizar un estudiante específico (opcional)
     * @return Student
     * @throws ApiException
     * @throws Throwable
     */
    public function updateOrCreateStudent(array $data, ?int $studentId = null): Student
    {
        // Get authenticated user
        /** @var User $currentUser */
        $currentUser = Auth::user();

        // Determinar qué estudiante actualizar
        $student = null;
        if ($studentId) {
            $student = $this->studentRepository->getOne(filters: ['id' => $studentId]);
            if (!$student) {
                throw new ApiException(message: 'Estudiante no encontrado', code: 404);
            }
        } else {
            // Get student associated with current user if exists
            $student = $this->studentRepository->getOne(filters: ['user_id' => $currentUser->id]);
        }

        try {
            DB::beginTransaction();

            if (!$student) {
                // Create new student
                $studentData = [
                    'user_id' => $currentUser->id,
                    'first_name' => $data['first_name'] ?? $currentUser->name,
                    'last_name' => $data['last_name'] ?? '',
                ];

                $student = $this->studentRepository->save($studentData);
                
                if (!$student) {
                    throw new ApiException(message: 'Error al crear estudiante', code: 500);
                }
            } else {
                // Update existing student
                $studentData = [
                    'first_name' => $data['first_name'] ?? $student->first_name,
                    'last_name' => $data['last_name'] ?? $student->last_name,
                ];

                $student = $this->studentRepository->save($studentData, $student);
                
                if (!$student) {
                    throw new ApiException(message: 'Error al actualizar estudiante', code: 500);
                }
            }

            // Update user info if provided
            if ($studentId === null || $student->user_id === $currentUser->id) {
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

            // Refresh the student model with the user relation
            return $this->studentRepository->getOne(
                filters: ['id' => $student->id],
                with: ['user']
            );

        } catch (Throwable $e) {
            DB::rollback();
            throw new ApiException(
                message: 'Error al actualizar/crear estudiante: ' . $e->getMessage(),
                code: 500
            );
        }
    }
    
    /**
     * Delete a student.
     *
     * @param int $studentId
     * @return bool
     * @throws ApiException
     * @throws Throwable
     */
    public function deleteStudent(int $studentId): bool
    {
        $student = $this->studentRepository->getOne(filters: ['id' => $studentId]);
        
        if (!$student) {
            throw new ApiException(message: 'Estudiante no encontrado', code: 404);
        }
        
        try {
            DB::beginTransaction();
            
            // Eliminar estudiante utilizando el método delete del repositorio
            $result = $this->studentRepository->delete($student);
            
            if (!$result) {
                throw new ApiException(message: 'Error al eliminar estudiante', code: 500);
            }
            
            DB::commit();
            return true;
        } catch (Throwable $e) {
            DB::rollback();
            throw new ApiException(
                message: 'Error al eliminar estudiante: ' . $e->getMessage(),
                code: 500
            );
        }
    }
}
