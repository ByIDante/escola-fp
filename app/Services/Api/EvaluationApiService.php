<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Domains\Evaluations\Repositories\EvaluationRepositoryInterface;
use App\Exceptions\ApiException;
use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class EvaluationApiService
{
    public function __construct(
        private EvaluationRepositoryInterface $evaluationRepository
    ) {
    }

    /**
     * Get all evaluations with pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return Paginator|LengthAwarePaginator|Builder
     */
    public function getAllEvaluations(array $filters = [], int $perPage = 10): Paginator|LengthAwarePaginator|Builder
    {
        return $this->evaluationRepository->list(
            with: ['student', 'teacher', 'module', 'unit'],
            filters: $filters,
            pagination: ['per_page' => $perPage]
        );
    }

    /**
     * Get specific Evaluation.
     *
     * @param int $evaluationId
     * @return Model|Evaluation
     * @throws ApiException
     */
    public function getEvaluation(int $evaluationId): Model|Evaluation
    {
        $evaluation = $this->evaluationRepository->getOne(
            filters: ['id' => $evaluationId],
            with: ['student', 'teacher', 'module', 'unit']
        );

        if (!$evaluation) {
            throw new ApiException(message: 'Evaluación no encontrada', code: 404);
        }

        return $evaluation;
    }

    /**
     * Update or Create Evaluation information
     *
     * @param array $data
     * @param int|null $evaluationId Para actualizar una evaluación específica (opcional)
     * @return Evaluation
     * @throws ApiException
     * @throws Throwable
     */
    public function updateOrCreateEvaluation(array $data, ?int $evaluationId = null): Evaluation
    {
        // Get authenticated user for logging/permissions
        /** @var User $currentUser */
        $currentUser = Auth::user();

        // Determinate if the evaluationId is provided
        $evaluation = null;
        if ($evaluationId) {
            $evaluation = $this->evaluationRepository->getOne(filters: ['id' => $evaluationId]);
            if (!$evaluation) {
                throw new ApiException(message: 'Evaluación no encontrada', code: 404);
            }

            // Verify permissions (only teachers can modify evaluations)
            if (!$currentUser->isTeacher()) {
                throw new ApiException(message: 'No tiene permisos para modificar evaluaciones', code: 403);
            }
        }

        try {
            DB::beginTransaction();

            if ($evaluation) {
                // Update the evaluation with the provided data
                $evaluationData = [
                    'student_id' => $data['student_id'] ?? $evaluation->student_id,
                    'teacher_id' => $data['teacher_id'] ?? $evaluation->teacher_id,
                    'module_id' => $data['module_id'] ?? $evaluation->module_id,
                    'unit_id' => $data['unit_id'] ?? $evaluation->unit_id,
                    'score' => $data['score'] ?? $evaluation->score,
                    'comments' => $data['comments'] ?? $evaluation->comments,
                    'evaluation_date' => $data['evaluation_date'] ?? $evaluation->evaluation_date,
                ];

                $evaluation = $this->evaluationRepository->save($evaluationData, $evaluation);

                if (!$evaluation) {
                    throw new ApiException(message: 'Error al actualizar evaluación', code: 500);
                }
            } else {
                // Create new evaluation with the provided data
                // Verify if required fields are missing
                if (
                    !isset($data['student_id']) || !isset($data['teacher_id']) ||
                    !isset($data['module_id']) || !isset($data['unit_id']) ||
                    !isset($data['score']) || !isset($data['evaluation_date'])
                ) {
                    throw new ApiException(message: 'Faltan campos obligatorios para crear la evaluación', code: 400);
                }

                // If the user is a teacher, use their ID as teacher_id if not specified
                if (!isset($data['teacher_id']) && $currentUser->isTeacher() && $currentUser->teacher) {
                    $data['teacher_id'] = $currentUser->teacher->id;
                }

                $evaluationData = [
                    'student_id' => $data['student_id'],
                    'teacher_id' => $data['teacher_id'],
                    'module_id' => $data['module_id'],
                    'unit_id' => $data['unit_id'],
                    'score' => $data['score'],
                    'comments' => $data['comments'] ?? null,
                    'evaluation_date' => $data['evaluation_date'],
                ];

                $evaluation = $this->evaluationRepository->save($evaluationData);

                if (!$evaluation) {
                    throw new ApiException(message: 'Error al crear evaluación', code: 500);
                }
            }

            DB::commit();

            // Refresh the evaluation model with relations
            return $this->evaluationRepository->getOne(
                filters: ['id' => $evaluation->id],
                with: ['student', 'teacher', 'module', 'unit']
            );

        } catch (ApiException $e) {
            DB::rollback();
            throw $e;
        } catch (Throwable $e) {
            DB::rollback();
            throw new ApiException(
                message: 'Error al actualizar/crear evaluación: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    /**
     * Delete an evaluation.
     *
     * @param int $evaluationId
     * @return bool
     * @throws ApiException
     * @throws Throwable
     */
    public function deleteEvaluation(int $evaluationId): bool
    {
        // Get authenticated user for permissions
        /** @var User $currentUser */
        $currentUser = Auth::user();

        // Verify permissions (only teachers can delete evaluations)
        if (!$currentUser->isTeacher()) {
            throw new ApiException(message: 'No tiene permisos para eliminar evaluaciones', code: 403);
        }

        $evaluation = $this->evaluationRepository->getOne(filters: ['id' => $evaluationId]);

        if (!$evaluation) {
            throw new ApiException(message: 'Evaluación no encontrada', code: 404);
        }

        try {
            DB::beginTransaction();

            // Delete the evaluation using the delete method from the repository
            $result = $this->evaluationRepository->delete($evaluation);

            if (!$result) {
                throw new ApiException(message: 'Error al eliminar evaluación', code: 500);
            }

            DB::commit();
            return true;
        } catch (Throwable $e) {
            DB::rollback();
            throw new ApiException(
                message: 'Error al eliminar evaluación: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    /**
     * Get evaluations for a specific student.
     *
     * @param int $studentId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getStudentEvaluations(int $studentId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $filters['student_id'] = $studentId;

        return $this->evaluationRepository->list(
            with: ['teacher', 'module', 'unit'],
            filters: $filters,
            pagination: ['per_page' => $perPage]
        );
    }

    /**
     * Get evaluations created by a specific teacher.
     *
     * @param int $teacherId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getTeacherEvaluations(int $teacherId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $filters['teacher_id'] = $teacherId;

        return $this->evaluationRepository->list(
            with: ['student', 'module', 'unit'],
            filters: $filters,
            pagination: ['per_page' => $perPage]
        );
    }
}
