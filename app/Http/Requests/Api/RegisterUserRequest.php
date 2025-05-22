<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\UserRoleEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Redirect;

final class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'sometimes|string|in:' . implode(',', UserRoleEnum::getValues()),
        ];

        // Validación condicional para datos de estudiante
        if ($this->input('role') === UserRoleEnum::STUDENT->value) {
            $rules['student_data.first_name'] = 'sometimes|required|string|max:255';
            $rules['student_data.last_name'] = 'sometimes|required|string|max:255';
        }

        // Validación condicional para datos de profesor
        if ($this->input('role') === UserRoleEnum::TEACHER->value) {
            $rules['teacher_data.first_name'] = 'sometimes|required|string|max:255';
            $rules['teacher_data.last_name'] = 'sometimes|required|string|max:255';
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
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'role.in' => 'El rol debe ser un valor válido: ' . implode(', ', UserRoleEnum::getValues()),
            'student_data.first_name.required' => 'El nombre del estudiante es obligatorio',
            'student_data.last_name.required' => 'El apellido del estudiante es obligatorio',
            'teacher_data.first_name.required' => 'El nombre del profesor es obligatorio',
            'teacher_data.last_name.required' => 'El apellido del profesor es obligatorio',
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
}
