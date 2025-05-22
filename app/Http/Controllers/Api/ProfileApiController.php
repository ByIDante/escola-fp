<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Requests\Api\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use App\Services\Api\ProfileApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * @group Profile Management
 *
 * APIs for managing user profile
 */
final class ProfileApiController extends BaseApiController
{
    public function __construct(
        private readonly ProfileApiService $profileApiService
    ) {
        parent::__construct();
    }

    /**
     * GET /api/profile
     *
     * @group Profile Endpoints
     *
     * Get the authenticated user's profile
     *
     * @return JsonResponse|UserResource
     */
    public function show(): JsonResponse|UserResource
    {
        $userId = Auth::id();
        try {
            $user = $this->profileApiService->getProfile(userId: $userId);
            return new UserResource($user);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * PATCH /api/profile
     *
     * @group Profile Endpoints
     *
     * Update the authenticated user's profile
     *
     * @param ProfileUpdateRequest $request
     * @return JsonResponse|UserResource
     */
    public function update(ProfileUpdateRequest $request): JsonResponse|UserResource
    {
        $data = $request->validated();
        try {
            $user = $this->profileApiService->updateProfile($data);
            return new UserResource($user);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }

    /**
     * DELETE /api/profile
     *
     * @group Profile Endpoints
     *
     * Delete the authenticated user's profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'password' => 'required|string',
        ]);

        try {
            $this->profileApiService->deleteProfile($validated['password']);
            return response()->json([
                'message' => 'Perfil eliminado correctamente'
            ], 200);
        } catch (ApiException $e) {
            return $this->incorrectResponse(errorCode: $e->getCode(), message: $e->getMessage());
        } catch (Throwable $e) {
            $this->logIncorrectResponse(exception: $e);
            return $this->incorrectResponse(errorCode: 0);
        }
    }
}
