<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Domains\Users\Repositories\UserRepositoryInterface;
use App\Enums\UserRoleEnum;
use App\Exceptions\ApiException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Throwable;

final class AuthApiService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * Login The User
     *
     * @param array $user_data
     * @return object
     * @throws ApiException
     */
    public function loginUser(array $user_data): object
    {
        /** @var User $user */
        $user = $this->userRepository->getOne(filters: ['email' => $user_data['email']]);

        if (!$user) {
            throw new ApiException(message: trans(key: 'user not found'), code: 1101);
        }

        if (!Hash::check($user_data['password'], $user->password)) {
            throw new ApiException(message: 'Credenciales incorrectas', code: 401);
        }

        return (object) [
            'status' => true,
            'message' => 'User Logged In Successfully',
            'token' => $user->createToken(name: "API TOKEN")->plainTextToken
        ];
    }

    /**
     * Register a new user.
     *
     * @param array $user_data
     * @return object
     * @throws ApiException
     */
    public function registerUser(array $user_data): object
    {
        try {
            // Verificar si el correo ya existe
            $existingUser = $this->userRepository->getOne(filters: ['email' => $user_data['email']]);
            if ($existingUser) {
                throw new ApiException(
                    message: 'Este correo electrónico ya está registrado',
                    code: 422
                );
            }

            // Crear el usuario
            $userData = [
                'name' => $user_data['name'],
                'email' => $user_data['email'],
                'password' => Hash::make($user_data['password']),
                'role' => $user_data['role'] ?? UserRoleEnum::STUDENT->value,
            ];

            /** @var User $user */
            $user = $this->userRepository->save($userData);

            // Crear perfil asociado según el rol
            if (isset($user_data['role'])) {
                $role = $user_data['role'];

                if ($role === UserRoleEnum::STUDENT->value && isset($user_data['student_data'])) {
                    $user->student()->create($user_data['student_data']);
                    $user->load('student');
                } elseif ($role === UserRoleEnum::TEACHER->value && isset($user_data['teacher_data'])) {
                    $user->teacher()->create($user_data['teacher_data']);
                    $user->load('teacher');
                }
            }

            // Generar token de autenticación
            $token = $user->createToken('API TOKEN')->plainTextToken;

            // Devolver en el mismo formato que loginUser
            return (object) [
                'status' => true,
                'message' => 'User Registered Successfully',
                'token' => $token,
                'new_user' => true,
                'user' => $user,
            ];
        } catch (ApiException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new ApiException(
                message: 'Error al registrar el usuario: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    /**
     * Logout The User
     *
     * @return void
     */
    public function logoutUser(): void
    {
        // Get the authenticated user
        /** @var User $currentUser */
        $currentUser = auth()->user();

        // Revoke only current token
        $currentUser->currentAccessToken()->delete();
    }
}
