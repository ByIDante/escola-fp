<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Redirect;

final class UpsertModuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Verify if the user is a teacher
        return $this->user()->isTeacher();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // Reglas base para actualización de módulo existente
        if ($this->isMethod('patch')) {
            return [
                'name' => 'sometimes|required|string|max:255|unique:modules,name,' . $this->moduleId,
            ];
        }
        
        // Reglas para creación de nuevo módulo
        return [
            'name' => 'required|string|max:255|unique:modules,name',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del módulo es obligatorio',
            'name.string' => 'El nombre del módulo debe ser texto',
            'name.max' => 'El nombre del módulo no puede exceder 255 caracteres',
            'name.unique' => 'Ya existe un módulo con este nombre',
        ];
    }

    /**
     * Customize Request response
     *
     * @param Validator $validator
     * @return void
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        $response = $this->expectsJson()
            ? new JsonResponse([
                'error' => [
                    'errorCode' => 1,
                    'message' => __('Validation error'),
                    'data' => $validator->errors(),
                ]
            ], 422)
            : Redirect::back()->withErrors($validator)->withInput();

        throw new ValidationException($validator, $response);
    }
    
    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     * @throws ValidationException
     */
    protected function failedAuthorization(): void
    {
        throw new ValidationException(validator: null, response: new JsonResponse([
            'error' => [
                'errorCode' => 403,
                'message' => 'No tiene permisos para crear o modificar módulos',
            ]
        ], 403));
    }
}
