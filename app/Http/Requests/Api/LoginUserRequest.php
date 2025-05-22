<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

final class LoginUserRequest extends FormRequest
{
    public function __construct()
    {
        parent::__construct();
    }

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
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ],
            default => [],
        };
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
                    'message' => 'Validation error.',
                    'data' => $validator->errors(),
                ]
            ], 422)
            : Redirect::back()->withErrors($validator)->withInput();

        throw new ValidationException($validator, $response);
    }
}
