<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureUserHasRole
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();
        $allowed = array_map(trim(...), explode(',', $roles));

        if ($user === null || ! in_array($user->role->value, $allowed, true)) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
