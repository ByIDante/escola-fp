<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Redirect;

final class UpsertTeacherRequest extends FormRequest
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
        return match ($this->method()) {
            'POST' => [
                // User data
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => ['required', 'confirmed', Password::min(8)],

                // Teacher data
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
            ],

            'PATCH', 'PUT' => [
                // User data
                'name' => 'sometimes|string|max:255',
                'email' => [
                    'sometimes',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($this->user()->id),
                ],
                'new_password' => ['sometimes', 'string', Password::min(8), 'confirmed'],

                // Teacher data
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
            ],

            default => [
                // User data
                'name' => 'sometimes|string|max:255',
                'email' => [
                    'sometimes',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($this->user()->id),
                ],
                'new_password' => ['sometimes', 'string', Password::min(8), 'confirmed'],

                // Teacher data
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
            ],
        };
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
            'email.unique' => 'Este correo electrónico ya está en uso',
            'password.required' => 'La contraseña es obligatoria',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'first_name.required' => 'El nombre es obligatorio',
            'last_name.required' => 'El apellido es obligatorio',
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
