<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property bool $status
 * @property string $message
 * @property string $token
 * @property bool $new_user
 * @property User|null $user
 */
final class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray(Request $request): array|JsonSerializable|Arrayable
    {
        $response = [
            'status' => $this->status,
            'message' => $this->message,
        ];

        // Añadir token si existe
        if (isset($this->token)) {
            $response['token'] = $this->token;
        }

        // Añadir si es un nuevo usuario
        if (isset($this->new_user)) {
            $response['new_user'] = $this->new_user;
        }

        // Añadir errores si existen
        if (isset($this->errors)) {
            $response['errors'] = $this->errors;
        }

        // Añadir información del usuario si está disponible
        if (isset($this->user)) {
            /** @var User $user */
            $user = $this->user;
            
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ];

            // Añadir perfil de estudiante si existe y está cargado
            if ($user->relationLoaded('student') && $user->student) {
                $userData['student'] = [
                    'id' => $user->student->id,
                    'first_name' => $user->student->first_name,
                    'last_name' => $user->student->last_name,
                ];
            }

            // Añadir perfil de profesor si existe y está cargado
            if ($user->relationLoaded('teacher') && $user->teacher) {
                $userData['teacher'] = [
                    'id' => $user->teacher->id,
                    'first_name' => $user->teacher->first_name,
                    'last_name' => $user->teacher->last_name,
                ];
            }

            $response['user'] = $userData;
        }

        return $response;
    }
}
