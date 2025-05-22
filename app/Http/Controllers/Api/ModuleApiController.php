<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Requests\Api\UpsertModuleRequest;
use App\Http\Resources\ModuleResource;
use App\Http\Resources\ModuleResourceCollection;
use App\Services\Api\ModuleApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * @tags Module
 */
final class ModuleApiController extends BaseApiController
{
    public function __construct(
        private readonly ModuleApiService $moduleApiService
    ) {
        parent::__construct();
    }

    /**
     * GET /api/modules
     *
     * @group Module Endpoints
     * @authenticated
     *
     * Obtiene listado de módulos
     *
     * @param Request $request
     * @return JsonResponse|ModuleResourceCollection
     */
    public function index(Request $request): JsonResponse|ModuleResourceCollection
    {
        try {
            $filters = $request->only(['search']);
            $modules = $this->moduleApiService->getAllModules(
                filters: $filters,
                perPage: (int) $request->input('per_page', 10)
            );
            return ModuleResourceCollection::make($modules);
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * POST /api/modules
     *
     * @group Module Endpoints
     * @authenticated
     *
     * Crea un nuevo módulo
     *
     * @param UpsertModuleRequest $request
     * @return JsonResponse|ModuleResource
     */
    public function store(UpsertModuleRequest $request): JsonResponse|ModuleResource
    {
        $data = $request->validated();
        try {
            $module = $this->moduleApiService->createModule(data: $data);
            return new ModuleResource($module);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * GET /api/modules/{moduleId}
     *
     * @group Module Endpoints
     * @authenticated
     *
     * Obtiene un módulo específico
     *
     * @param int $moduleId
     * @return JsonResponse|ModuleResource
     */
    public function show(int $moduleId): JsonResponse|ModuleResource
    {
        try {
            $module = $this->moduleApiService->getModule(moduleId: $moduleId);
            return new ModuleResource($module);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * PATCH /api/modules/{moduleId}
     *
     * @group Module Endpoints
     * @authenticated
     *
     * Actualiza un módulo existente
     *
     * @param UpsertModuleRequest $request
     * @param int $moduleId
     * @return JsonResponse|ModuleResource
     */
    public function update(UpsertModuleRequest $request, int $moduleId): JsonResponse|ModuleResource
    {
        $data = $request->validated();
        try {
            $updatedModule = $this->moduleApiService->updateModule(
                data: $data,
                moduleId: $moduleId
            );
            return new ModuleResource($updatedModule);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * DELETE /api/modules/{moduleId}
     *
     * @group Module Endpoints
     * @authenticated
     *
     * Elimina un módulo
     *
     * @param int $moduleId
     * @return JsonResponse
     */
    public function destroy(int $moduleId): JsonResponse
    {
        try {
            $this->moduleApiService->deleteModule(moduleId: $moduleId);
            return response()->json([
                'message' => 'Módulo eliminado correctamente'
            ], 200);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * GET /api/modules/{moduleId}/units
     *
     * @group Module Endpoints
     * @authenticated
     *
     * Obtiene las unidades de un módulo específico
     *
     * @param int $moduleId
     * @return JsonResponse
     */
    public function getModuleUnits(int $moduleId): JsonResponse
    {
        try {
            $units = $this->moduleApiService->getModuleUnits(moduleId: $moduleId);
            return response()->json([
                'data' => $units
            ], 200);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }
}
