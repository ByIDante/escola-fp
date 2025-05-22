<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Domains\Students\Repositories\StudentRepositoryInterface;
use App\Domains\Teachers\Repositories\TeacherRepositoryInterface;
use App\Domains\Users\Repositories\UserRepositoryInterface;
use App\Enums\UserRoleEnum;
use App\Exceptions\ApiException;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

final readonly class ProfileApiService
{
    public function __construct(
        private StudentRepositoryInterface $studentRepository,
        private TeacherRepositoryInterface $teacherRepository,
        private UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * Get the authenticated user's profile
     *
     * @param int $userId
     * @return Model
     * @throws ApiException
     */
    public function getProfile(int $userId): Model
    {
        // Get the user using the repository with relations
        $user = $this->userRepository->getOne(
            filters: ['id' => $userId]
        );

        if (!$user) {
            throw new ApiException(message: 'Usuario no encontrado', code: 404);
        }

        // Load relations based on user role
        if ($user->role === UserRoleEnum::STUDENT->value) {
            // Load relation of student with its evaluations
            $user->load(['student', 'student.evaluations']);
        } elseif ($user->role === UserRoleEnum::TEACHER->value) {
            // Load relation of teacher with its units and evaluations
            $user->load(['teacher', 'teacher.units', 'teacher.evaluations']);
        }

        return $user;
    }

    /**
     * Update the authenticated user's profile
     *
     * @param array $data
     * @return User
     * @throws ApiException
     * @throws Throwable
     */
    public function updateProfile(array $data): User
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            throw new ApiException(message: 'Usuario no autenticado', code: 401);
        }

        try {
            DB::beginTransaction();

            // Actualizar datos básicos de usuario
            $userUpdateData = [];

            if (isset($data['name'])) {
                $userUpdateData['name'] = $data['name'];
            }

            if (isset($data['password'])) {
                // Verificar contraseña actual
                if (!isset($data['current_password']) || !Hash::check($data['current_password'], $user->password)) {
                    throw new ApiException(message: 'La contraseña actual es incorrecta', code: 401);
                }
                $userUpdateData['password'] = Hash::make($data['password']);
            }

            if (!empty($userUpdateData)) {
                $this->userRepository->save($userUpdateData, $user);
            }

            // Actualizar datos específicos según el rol
            if ($user->role === UserRoleEnum::STUDENT->value) {
                $this->updateStudentProfile($user, $data);
            } elseif ($user->role === UserRoleEnum::TEACHER->value) {
                $this->updateTeacherProfile($user, $data);
            }

            DB::commit();

            // Recargar el usuario con sus relaciones utilizando el repositorio
            /** @var User $updatedUser */
            $updatedUser = $this->userRepository->getOne(['id' => $user->id]);
            $this->loadUserRelations($updatedUser);

            return $updatedUser;

        } catch (ApiException $e) {
            DB::rollBack();
            throw $e;
        } catch (Throwable $e) {
            DB::rollBack();
            throw new ApiException(
                message: 'Error al actualizar el perfil: ' . $e->getMessage(),
                code: 500
            );
        }
    }


    /**
     * Delete the authenticated user's profile
     *
     * @param string $password
     * @return bool
     * @throws ApiException
     * @throws Throwable
     */
    public function deleteProfile(string $password): bool
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            throw new ApiException(message: 'Usuario no autenticado', code: 401);
        }

        if (!Hash::check($password, $user->password)) {
            throw new ApiException(message: 'La contraseña es incorrecta', code: 401);
        }

        try {
            DB::beginTransaction();

            // Eliminar tokens
            $user->tokens()->delete();

            // Eliminar relaciones asociadas basadas en el rol
            if ($user->role === UserRoleEnum::STUDENT->value && $user->student) {
                $this->studentRepository->delete($user->student);
            } elseif ($user->role === UserRoleEnum::TEACHER->value && $user->teacher) {
                $this->teacherRepository->delete($user->teacher);
            }

            // Eliminar usuario
            $user->delete();

            DB::commit();
            return true;

        } catch (Throwable $e) {
            DB::rollBack();
            throw new ApiException(
                message: 'Error al eliminar el perfil: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    /**
     * Load relations based on user role
     *
     * @param User $user
     * @return void
     */
    private function loadUserRelations(User $user): void
    {
        if ($user->role === UserRoleEnum::STUDENT->value) {
            $user->load('student');
        } elseif ($user->role === UserRoleEnum::TEACHER->value) {
            $user->load('teacher');
        }
    }

    /**
     * Update student profile data
     *
     * @param User $user
     * @param array $data
     * @return Student|null
     */
    private function updateStudentProfile(User $user, array $data): ?Student
    {
        if (!isset($data['first_name']) && !isset($data['last_name'])) {
            return $user->student;
        }

        $student = $this->studentRepository->getOne(['user_id' => $user->id]);

        if (!$student) {
            // Crear nuevo perfil de estudiante si no existe
            $studentData = [
                'user_id' => $user->id,
                'first_name' => $data['first_name'] ?? $user->name,
                'last_name' => $data['last_name'] ?? '',
            ];

            return $this->studentRepository->save($studentData);
        }

        // Actualizar perfil existente
        $studentData = [];

        if (isset($data['first_name'])) {
            $studentData['first_name'] = $data['first_name'];
        }

        if (isset($data['last_name'])) {
            $studentData['last_name'] = $data['last_name'];
        }

        return $this->studentRepository->save($studentData, $student);
    }

    /**
     * Update teacher profile data
     *
     * @param User $user
     * @param array $data
     * @return Teacher|null
     */
    private function updateTeacherProfile(User $user, array $data): ?Teacher
    {
        if (!isset($data['first_name']) && !isset($data['last_name'])) {
            return $user->teacher;
        }

        $teacher = $this->teacherRepository->getOne(['user_id' => $user->id]);

        if (!$teacher) {
            // Crear nuevo perfil de profesor si no existe
            $teacherData = [
                'user_id' => $user->id,
                'first_name' => $data['first_name'] ?? $user->name,
                'last_name' => $data['last_name'] ?? '',
            ];

            return $this->teacherRepository->save($teacherData);
        }

        // Actualizar perfil existente
        $teacherData = [];

        if (isset($data['first_name'])) {
            $teacherData['first_name'] = $data['first_name'];
        }

        if (isset($data['last_name'])) {
            $teacherData['last_name'] = $data['last_name'];
        }

        return $this->teacherRepository->save($teacherData, $teacher);
    }
}
