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

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié.'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user->role) {
            return response()->json(['message' => 'Aucun rôle assigné à cet utilisateur.'], Response::HTTP_FORBIDDEN);
        }

        $permissions = is_array($user->role->permissions) ? $user->role->permissions : explode(',', $user->role->permissions ?? '');


        if (!in_array($permission, $permissions)) {
            return response()->json(['message' => "Accès refusé. Permission '$permission' manquante."], Response::HTTP_FORBIDDEN);
        }


        return $next($request);
    }
}
