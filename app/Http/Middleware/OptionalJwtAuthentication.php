<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Authenticates JWT when a Bearer token is present; does not reject guests.
 */
final class OptionalJwtAuthentication
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->bearerToken() !== null) {
            try {
                $user = JWTAuth::parseToken()->authenticate();
                if ($user !== null) {
                    auth('api')->setUser($user);
                    $request->setUserResolver(static fn () => $user);
                }
            } catch (JWTException) {
                // Invalid or expired token — continue as guest on public routes.
            }
        }

        return $next($request);
    }
}
