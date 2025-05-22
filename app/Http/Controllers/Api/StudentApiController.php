<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Requests\Api\UpsertStudentRequest;
use App\Http\Resources\StudentResource;
use App\Http\Resources\StudentResourceCollection;
use App\Services\Api\StudentApiService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * @tags Student
 */
final class StudentApiController extends BaseApiController
{
    public function __construct(
        private readonly StudentApiService $studentApiService
    ) {
        parent::__construct();
    }

    /**
     * GET /api/students
     *
     * @group Student Endpoints
     * @authenticated
     *
     * Obtiene listado de estudiantes
     *
     * @param Request $request
     * @return JsonResponse|StudentResourceCollection
     */
    public function index(Request $request): JsonResponse|StudentResourceCollection
    {
        try {
            $filters = $request->only(['search']);
            $students = $this->studentApiService->getAllStudents(
                filters: $filters,
                perPage: (int) $request->input('per_page', 10)
            );
            return StudentResourceCollection::make($students);
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * POST /api/students
     *
     * @group Student Endpoints
     * @authenticated
     *
     * Crea un nuevo estudiante
     *
     * @param UpsertStudentRequest $request
     * @return JsonResponse|StudentResource
     */
    public function store(UpsertStudentRequest $request): JsonResponse|StudentResource
    {
        return $this->upsertStudent($request);
    }

    /**
     * GET /api/students/{studentId}
     *
     * @group Student Endpoints
     * @authenticated
     *
     * Obtiene un estudiante específico
     *
     * @param int $studentId
     * @return JsonResponse|StudentResource
     */
    public function show(int $studentId): JsonResponse|StudentResource
    {
        try {
            $student = $this->studentApiService->getStudent(studentId: $studentId);
            return new StudentResource($student);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * PATCH /api/students/{studentId}
     *
     * @group Student Endpoints
     * @authenticated
     *
     * Actualiza un estudiante existente
     *
     * @param UpsertStudentRequest $request
     * @param int $studentId
     * @return JsonResponse|StudentResource
     */
    public function update(UpsertStudentRequest $request, int $studentId): JsonResponse|StudentResource
    {
        $data = $request->validated();
        try {
            $updatedStudent = $this->studentApiService->updateOrCreateStudent(
                data: $data,
                studentId: $studentId
            );
            return new StudentResource($updatedStudent);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * DELETE /api/students/{studentId}
     *
     * @group Student Endpoints
     * @authenticated
     *
     * Elimina un estudiante
     *
     * @param int $studentId
     * @return JsonResponse
     */
    public function destroy(int $studentId): JsonResponse
    {
        try {
            $this->studentApiService->deleteStudent(studentId: $studentId);
            return response()->json([
                'message' => 'Estudiante eliminado correctamente'
            ], 200);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * GET /api/student
     *
     * @group Student Endpoints
     * @authenticated
     *
     * Obtiene el estudiante del usuario autenticado
     *
     * @return StudentResource|JsonResponse
     */
    public function getStudent(): JsonResponse|StudentResource
    {
        try {
            $student = $this->studentApiService->getStudent();
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Exception $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }

        return new StudentResource(resource: $student);
    }

    /**
     * POST /api/profile/create
     * POST /api/profile/update
     *
     * @group Student Endpoints
     * @authenticated
     *
     * Crea o actualiza el perfil de estudiante del usuario autenticado
     *
     * @param UpsertStudentRequest $request
     * @param int|null $studentId
     * @return JsonResponse|StudentResource
     */
    public function upsertStudent(UpsertStudentRequest $request, ?int $studentId = null): JsonResponse|StudentResource
    {
        $data = $request->validated();
        try {
            $student = $this->studentApiService->updateOrCreateStudent(
                data: $data,
                studentId: $studentId
            );
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }

        return new StudentResource(resource: $student);
    }
}
