<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{   
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'user not authenticated'], 401);
        }

        $userRoleName = $user->roles?->name;

        if (!$userRoleName) {
            return response()->json(['error' => 'access denied'], 403);
        }

        if ($userRoleName === 'ADMIN') { 
            return $next($request);
        }

        
        if (!in_array($userRoleName, $roles)) {
            return response()->json([
                'error' => 'access denied'
            ], 403); 
        }

        return $next($request);
    }
}