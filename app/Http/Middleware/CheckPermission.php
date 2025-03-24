<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user();

        if (!$user || !$user->role || !in_array($permission, explode(',', $user->role->permissions))) {
            return response()->json(['message' => 'Accès refusé. Permission manquante.'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
