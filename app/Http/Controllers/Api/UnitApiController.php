<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Requests\Api\UpsertUnitRequest;
use App\Http\Resources\UnitResource;
use App\Http\Resources\UnitResourceCollection;
use App\Services\Api\UnitApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * @tags Unit
 */
final class UnitApiController extends BaseApiController
{
    public function __construct(
        private readonly UnitApiService $unitApiService
    ) {
        parent::__construct();
    }

    /**
     * GET /api/units
     *
     * @group Unit Endpoints
     * @authenticated
     *
     * Obtiene listado de unidades
     *
     * @param Request $request
     * @return JsonResponse|UnitResourceCollection
     */
    public function index(Request $request): JsonResponse|UnitResourceCollection
    {
        try {
            $filters = $request->only(['search', 'module_id', 'teacher_id']);
            $units = $this->unitApiService->getAllUnits(
                filters: $filters,
                perPage: (int) $request->input('per_page', 10)
            );
            return UnitResourceCollection::make($units);
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * POST /api/units
     *
     * @group Unit Endpoints
     * @authenticated
     *
     * Crea una nueva unidad
     *
     * @param UpsertUnitRequest $request
     * @return JsonResponse|UnitResource
     */
    public function store(UpsertUnitRequest $request): JsonResponse|UnitResource
    {
        $data = $request->validated();
        try {
            $unit = $this->unitApiService->createUnit(data: $data);
            return new UnitResource($unit);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * GET /api/units/{unitId}
     *
     * @group Unit Endpoints
     * @authenticated
     *
     * Obtiene una unidad específica
     *
     * @param int $unitId
     * @return JsonResponse|UnitResource
     */
    public function show(int $unitId): JsonResponse|UnitResource
    {
        try {
            $unit = $this->unitApiService->getUnit(unitId: $unitId);
            return new UnitResource($unit);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * PATCH /api/units/{unitId}
     *
     * @group Unit Endpoints
     * @authenticated
     *
     * Actualiza una unidad existente
     *
     * @param UpsertUnitRequest $request
     * @param int $unitId
     * @return JsonResponse|UnitResource
     */
    public function update(UpsertUnitRequest $request, int $unitId): JsonResponse|UnitResource
    {
        $data = $request->validated();
        try {
            
            $updatedUnit = $this->unitApiService->updateUnit(
                data: $data,
                unitId: $unitId
            );
            return new UnitResource($updatedUnit);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * DELETE /api/units/{unitId}
     *
     * @group Unit Endpoints
     * @authenticated
     *
     * Elimina una unidad
     *
     * @param int $unitId
     * @return JsonResponse
     */
    public function destroy(int $unitId): JsonResponse
    {
        try {
            $this->unitApiService->deleteUnit(unitId: $unitId);
            return response()->json([
                'message' => 'Unidad eliminada correctamente'
            ], 200);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * GET /api/units/{unitId}/evaluations
     *
     * @group Unit Endpoints
     * @authenticated
     *
     * Obtiene las evaluaciones de una unidad específica
     *
     * @param Request $request
     * @param int $unitId
     * @return JsonResponse
     */
    public function getUnitEvaluations(Request $request, int $unitId): JsonResponse
    {
        try {
            $filters = $request->only(['student_id', 'teacher_id']);
            $evaluations = $this->unitApiService->getUnitEvaluations(
                unitId: $unitId,
                filters: $filters,
                perPage: (int) $request->input('per_page', 10)
            );
            return response()->json([
                'data' => $evaluations
            ], 200);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }
}
