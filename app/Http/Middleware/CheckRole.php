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
            return response()->json(['error' => 'Usuário não autenticado.'], 401);
        }

        if ($user->role === 'admin') { 
            return $next($request);
        }

        
        if (!in_array($user->role, $roles)) {
            return response()->json([
                'error' => 'Acesso negado. Seu nível de usuário não permite esta ação.'
            ], 403); 
        }

        return $next($request);
    }
}