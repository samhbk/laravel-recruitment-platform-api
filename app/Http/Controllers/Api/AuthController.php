<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Auth\LoginUserAction;
use App\Actions\Auth\RegisterUserAction;
use App\Domain\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\AuthTokenResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUserAction $action): JsonResponse
    {
        $validated = $request->validated();
        $role = UserRole::tryFrom($validated['role'] ?? '') ?? UserRole::JobSeeker;
        $result = $action->execute(
            $validated['name'],
            $validated['email'],
            $validated['password'],
            $role,
        );

        return (new AuthTokenResource([
            'access_token' => $result['access_token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
            'user' => $result['user'],
        ]))
            ->response()
            ->setStatusCode(201);
    }

    public function login(LoginRequest $request, LoginUserAction $action): JsonResponse|AuthTokenResource
    {
        $result = $action->execute($request->only('email', 'password'));

        if ($result === null) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return new AuthTokenResource($result);
    }

    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh(): AuthTokenResource
    {
        $token = Auth::guard('api')->refresh();

        return new AuthTokenResource([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return (new UserResource($request->user()))->response()->setStatusCode(200);
    }
}
