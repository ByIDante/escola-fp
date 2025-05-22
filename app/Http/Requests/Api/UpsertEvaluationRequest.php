<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Redirect;

final class UpsertEvaluationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Verificar si el usuario es un profesor o administrador
        return $this->user() && ($this->user()->isTeacher());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // Reglas base para actualización de evaluación existente
        if ($this->isMethod('patch')) {
            return [
                'student_id' => 'sometimes|required|integer|exists:students,id',
                'teacher_id' => 'sometimes|required|integer|exists:teachers,id',
                'module_id' => 'sometimes|required|integer|exists:modules,id',
                'unit_id' => 'sometimes|required|integer|exists:units,id',
                'score' => 'sometimes|required|numeric|min:0|max:10',
                'comments' => 'sometimes|nullable|string|max:1000',
                'evaluation_date' => 'sometimes|required|date',
            ];
        }
        
        // Reglas para creación de nueva evaluación
        return [
            'student_id' => 'required|integer|exists:students,id',
            'teacher_id' => 'required|integer|exists:teachers,id',
            'module_id' => 'required|integer|exists:modules,id',
            'unit_id' => 'required|integer|exists:units,id',
            'score' => 'required|numeric|min:0|max:10',
            'comments' => 'nullable|string|max:1000',
            'evaluation_date' => 'required|date',
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
            'student_id.required' => 'El estudiante es obligatorio',
            'student_id.exists' => 'El estudiante seleccionado no existe',
            'teacher_id.required' => 'El profesor es obligatorio',
            'teacher_id.exists' => 'El profesor seleccionado no existe',
            'module_id.required' => 'El módulo es obligatorio',
            'module_id.exists' => 'El módulo seleccionado no existe',
            'unit_id.required' => 'La unidad es obligatoria',
            'unit_id.exists' => 'La unidad seleccionada no existe',
            'score.required' => 'La puntuación es obligatoria',
            'score.numeric' => 'La puntuación debe ser un número',
            'score.min' => 'La puntuación mínima es 0',
            'score.max' => 'La puntuación máxima es 10',
            'comments.max' => 'Los comentarios no pueden exceder 1000 caracteres',
            'evaluation_date.required' => 'La fecha de evaluación es obligatoria',
            'evaluation_date.date' => 'La fecha de evaluación debe ser una fecha válida',
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
                'message' => 'No tiene permisos para crear o modificar evaluaciones',
            ]
        ], 403));
    }
}
