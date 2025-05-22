<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Requests\Api\UpsertEvaluationRequest;
use App\Http\Resources\EvaluationResource;
use App\Http\Resources\EvaluationResourceCollection;
use App\Services\Api\EvaluationApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * @tags Evaluation
 */
final class EvaluationApiController extends BaseApiController
{
    public function __construct(
        private readonly EvaluationApiService $evaluationApiService
    ) {
        parent::__construct();
    }

    /**
     * GET /api/evaluations
     *
     * @group Evaluation Endpoints
     * @authenticated
     *
     * Obtiene listado de evaluaciones
     *
     * @param Request $request
     * @return JsonResponse|EvaluationResourceCollection
     */
    public function index(Request $request): JsonResponse|EvaluationResourceCollection
    {
        try {
            $filters = $request->only(['search', 'student_id', 'teacher_id', 'module_id', 'unit_id']);
            $evaluations = $this->evaluationApiService->getAllEvaluations(
                filters: $filters,
                perPage: (int) $request->input('per_page', 10)
            );
            return EvaluationResourceCollection::make($evaluations);
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * POST /api/evaluations
     *
     * @group Evaluation Endpoints
     * @authenticated
     *
     * Crea una nueva evaluación
     *
     * @param UpsertEvaluationRequest $request
     * @return JsonResponse|EvaluationResource
     */
    public function store(UpsertEvaluationRequest $request): JsonResponse|EvaluationResource
    {
        $data = $request->validated();
        try {
            $evaluation = $this->evaluationApiService->updateOrCreateEvaluation(data: $data);
            return new EvaluationResource($evaluation);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * GET /api/evaluations/{evaluationId}
     *
     * @group Evaluation Endpoints
     * @authenticated
     *
     * Obtiene una evaluación específica
     *
     * @param int $evaluationId
     * @return JsonResponse|EvaluationResource
     */
    public function show(int $evaluationId): JsonResponse|EvaluationResource
    {
        try {
            $evaluation = $this->evaluationApiService->getEvaluation(evaluationId: $evaluationId);
            return new EvaluationResource($evaluation);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * PATCH /api/evaluations/{evaluationId}
     *
     * @group Evaluation Endpoints
     * @authenticated
     *
     * Actualiza una evaluación existente
     *
     * @param UpsertEvaluationRequest $request
     * @param int $evaluationId
     * @return JsonResponse|EvaluationResource
     */
    public function update(UpsertEvaluationRequest $request, int $evaluationId): JsonResponse|EvaluationResource
    {
        $data = $request->validated();
        try {
            $updatedEvaluation = $this->evaluationApiService->updateOrCreateEvaluation(
                data: $data,
                evaluationId: $evaluationId
            );
            return new EvaluationResource($updatedEvaluation);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * DELETE /api/evaluations/{evaluationId}
     *
     * @group Evaluation Endpoints
     * @authenticated
     *
     * Elimina una evaluación
     *
     * @param int $evaluationId
     * @return JsonResponse
     */
    public function destroy(int $evaluationId): JsonResponse
    {
        try {
            $this->evaluationApiService->deleteEvaluation(evaluationId: $evaluationId);
            return response()->json([
                'message' => 'Evaluación eliminada correctamente'
            ], 200);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }
    
    /**
     * GET /api/students/{studentId}/evaluations
     *
     * @group Evaluation Endpoints
     * @authenticated
     *
     * Obtiene evaluaciones de un estudiante específico
     *
     * @param Request $request
     * @param int $studentId
     * @return JsonResponse|EvaluationResourceCollection
     */
    public function getStudentEvaluations(Request $request, int $studentId): JsonResponse|EvaluationResourceCollection
    {
        try {
            $filters = $request->only(['module_id', 'unit_id']);
            $evaluations = $this->evaluationApiService->getStudentEvaluations(
                studentId: $studentId,
                filters: $filters,
                perPage: (int) $request->input('per_page', 10)
            );
            return EvaluationResourceCollection::make($evaluations);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }
    
    /**
     * GET /api/teachers/{teacherId}/evaluations
     *
     * @group Evaluation Endpoints
     * @authenticated
     *
     * Obtiene evaluaciones creadas por un profesor específico
     *
     * @param Request $request
     * @param int $teacherId
     * @return JsonResponse|EvaluationResourceCollection
     */
    public function getTeacherEvaluations(Request $request, int $teacherId): JsonResponse|EvaluationResourceCollection
    {
        try {
            $filters = $request->only(['student_id', 'module_id', 'unit_id']);
            $evaluations = $this->evaluationApiService->getTeacherEvaluations(
                teacherId: $teacherId,
                filters: $filters,
                perPage: (int) $request->input('per_page', 10)
            );
            return EvaluationResourceCollection::make($evaluations);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }
}
