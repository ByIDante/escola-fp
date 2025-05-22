<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Requests\Api\UpsertTeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Http\Resources\TeacherResourceCollection;
use App\Services\Api\TeacherApiService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * @tags Teacher
 */
final class TeacherApiController extends BaseApiController
{
    public function __construct(
        private readonly TeacherApiService $teacherApiService
    ) {
        parent::__construct();
    }

    /**
     * GET /api/teachers
     *
     * @group Teacher Endpoints
     * @authenticated
     *
     * Obtiene listado de profesores
     *
     * @param Request $request
     * @return JsonResponse|TeacherResourceCollection
     */
    public function index(Request $request): JsonResponse|TeacherResourceCollection
    {
        try {
            $filters = $request->only(['search']);
            $teachers = $this->teacherApiService->getAllTeachers(
                filters: $filters,
                perPage: (int) $request->input('per_page', 10)
            );
            return TeacherResourceCollection::make($teachers);
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * POST /api/teachers
     *
     * @group Teacher Endpoints
     * @authenticated
     *
     * Crea un nuevo profesor
     *
     * @param UpsertTeacherRequest $request
     * @return JsonResponse|TeacherResource
     */
    public function store(UpsertTeacherRequest $request): JsonResponse|TeacherResource
    {
        return $this->upsertTeacher($request);
    }

    /**
     * GET /api/teachers/{teacherId}
     *
     * @group Teacher Endpoints
     * @authenticated
     *
     * Obtiene un profesor específico
     *
     * @param int $teacherId
     * @return JsonResponse|TeacherResource
     */
    public function show(int $teacherId): JsonResponse|TeacherResource
    {
        try {
            $teacher = $this->teacherApiService->getTeacher(teacherId: $teacherId);
            return new TeacherResource($teacher);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * PATCH /api/teachers/{teacherId}
     *
     * @group Teacher Endpoints
     * @authenticated
     *
     * Actualiza un profesor existente
     *
     * @param UpsertTeacherRequest $request
     * @param int $teacherId
     * @return JsonResponse|TeacherResource
     */
    public function update(UpsertTeacherRequest $request, int $teacherId): JsonResponse|TeacherResource
    {
        $data = $request->validated();
        try {
            $updatedTeacher = $this->teacherApiService->updateOrCreateTeacher(
                data: $data,
                teacherId: $teacherId
            );
            return new TeacherResource($updatedTeacher);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * DELETE /api/teachers/{teacherId}
     *
     * @group Teacher Endpoints
     * @authenticated
     *
     * Elimina un profesor
     *
     * @param int $teacherId
     * @return JsonResponse
     */
    public function destroy(int $teacherId): JsonResponse
    {
        try {
            $this->teacherApiService->deleteTeacher(teacherId: $teacherId);
            return response()->json([
                'message' => 'Profesor eliminado correctamente'
            ], 200);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * GET /api/teacher
     *
     * @group Teacher Endpoints
     * @authenticated
     *
     * Obtiene el profesor del usuario autenticado
     *
     * @return TeacherResource|JsonResponse
     */
    public function getTeacher(): JsonResponse|TeacherResource
    {
        try {
            $teacher = $this->teacherApiService->getTeacher();
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Exception $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }

        return new TeacherResource(resource: $teacher);
    }

    /**
     * POST /api/profile/create-teacher
     * POST /api/profile/update-teacher
     *
     * @group Teacher Endpoints
     * @authenticated
     *
     * Crea o actualiza el perfil de profesor del usuario autenticado
     *
     * @param UpsertTeacherRequest $request
     * @param int|null $teacherId
     * @return JsonResponse|TeacherResource
     */
    public function upsertTeacher(UpsertTeacherRequest $request, ?int $teacherId = null): JsonResponse|TeacherResource
    {
        $data = $request->validated();
        try {
            $teacher = $this->teacherApiService->updateOrCreateTeacher(
                data: $data,
                teacherId: $teacherId
            );
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }

        return new TeacherResource(resource: $teacher);
    }
}
