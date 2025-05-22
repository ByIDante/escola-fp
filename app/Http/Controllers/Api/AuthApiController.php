<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Http\Resources\AuthResource;
use App\Http\Responses\NoContentResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Services\Api\AuthApiService;

/**
 * @tags Auth
 */
final class AuthApiController extends BaseApiController
{
    public function __construct(
        private readonly AuthApiService $authApiService
    ) {
        parent::__construct();
    }

    /**
     * POST /api/login
     *
     * @group Auth Endpoints
     *
     * @param LoginUserRequest $request
     * @return AuthResource|JsonResponse
     *
     * Login The User
     */
    public function login(LoginUserRequest $request): JsonResponse|AuthResource
    {
        try {
            $auth_resource = $this->authApiService->loginUser(user_data: $request->validated());
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Exception $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }

        return new AuthResource(resource: $auth_resource);
    }

    /**
     * POST /api/register
     *
     * @group Auth Endpoints
     *
     * @param RegisterUserRequest $request
     * @return AuthResource|JsonResponse
     *
     * Register a new user
     */
    public function register(RegisterUserRequest $request): JsonResponse|AuthResource
    {
        try {
            $auth_resource = $this->authApiService->registerUser(user_data: $request->validated());
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Exception $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }

        return new AuthResource(resource: $auth_resource);
    }

    /**
     * POST /api/logout
     *
     * @group Auth Endpoints
     * @authenticated
     *
     * @return NoContentResponse|JsonResponse
     *
     * Logout The User
     */
    public function logout(): NoContentResponse|JsonResponse
    {
        try {
            $this->authApiService->logoutUser();
            return new NoContentResponse();
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Exception $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }
}
