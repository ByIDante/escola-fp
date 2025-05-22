<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read User $resource
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;
        
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
        ];
        
        // Añadir datos del estudiante si existe y el rol es estudiante
        if ($user->role === UserRoleEnum::STUDENT->value && $user->relationLoaded('student') && $user->student) {
            $data['student'] = [
                'id' => $user->student->id,
                'first_name' => $user->student->first_name,
                'last_name' => $user->student->last_name,
            ];
        }
        
        // Añadir datos del profesor si existe y el rol es profesor
        if ($user->role === UserRoleEnum::TEACHER->value && $user->relationLoaded('teacher') && $user->teacher) {
            $data['teacher'] = [
                'id' => $user->teacher->id,
                'first_name' => $user->teacher->first_name,
                'last_name' => $user->teacher->last_name,
            ];
        }
        
        return $data;
    }
}
