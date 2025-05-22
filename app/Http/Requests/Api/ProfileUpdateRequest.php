<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Redirect;

final class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Usuario
            'name' => 'sometimes|string|max:255',
            'current_password' => 'sometimes|required_with:email,password|string',
            'password' => ['sometimes', 'string', Password::min(8), 'confirmed'],
            
            // Perfil (común para estudiantes y profesores)
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
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
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'Este correo electrónico ya está en uso',
            'current_password.required_with' => 'La contraseña actual es obligatoria para cambiar el email o la contraseña',
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
