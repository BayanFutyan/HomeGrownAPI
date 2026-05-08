<?php

namespace App\Http\Middleware;

use App\Enums\UserRoleEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $userRole = $user->role instanceof UserRoleEnum
            ? $user->role->value
            : $user->role;

        if (!in_array($userRole, $roles)) {
            return response()->json([
                'message' => 'Forbidden. You do not have permission.'
            ], 403);
        }

        return $next($request);
    }
}