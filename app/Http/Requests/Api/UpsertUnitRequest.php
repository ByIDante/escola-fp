<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Redirect;

final class UpsertUnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $user = $this->user();

        // Si es profesor y está intentando actualizar una unidad
        if ($user && $user->isTeacher() && $this->isMethod('patch') && $this->unitId) {
            // Verificar si es el profesor asignado a esta unidad
            $unit = \App\Models\Unit::find($this->unitId);
            return $unit && $unit->teacher_id === $user->teacher->id;
        }

        // Si es profesor y está creando una unidad
        if ($user && $user->isTeacher() && $this->isMethod('post')) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $user = Auth::user();
        $isTeacher = $user && $user->isTeacher();

        // Reglas base para actualización de unidad existente
        if ($this->isMethod('patch')) {
            // Si es profesor, solo puede modificar el nombre
            return [
                'title' => 'sometimes|required|string|max:255',
            ];
        }

        // Reglas para creación de nueva unidad
        $rules = [
            'title' => 'required|string|max:255',
            'module_id' => 'required|integer|exists:modules,id',
        ];

        // Si es admin, puede especificar el profesor
        if ($isTeacher) {
            $rules['teacher_id'] = 'required|integer|exists:teachers,id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El nombre de la unidad es obligatorio',
            'title.string' => 'El nombre de la unidad debe ser texto',
            'title.max' => 'El nombre de la unidad no puede exceder 255 caracteres',
            'module_id.required' => 'El módulo es obligatorio',
            'module_id.exists' => 'El módulo seleccionado no existe',
            'teacher_id.required' => 'El profesor es obligatorio',
            'teacher_id.exists' => 'El profesor seleccionado no existe',
        ];
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization(): void
    {
        if ($this->expectsJson()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                new JsonResponse([
                    'error' => [
                        'errorCode' => 403,
                        'message' => 'No tiene permisos para crear o modificar esta unidad',
                    ]
                ], 403)
            );
        }

        throw new \Illuminate\Auth\Access\AuthorizationException('No tiene permisos para crear o modificar esta unidad');
    }
}
