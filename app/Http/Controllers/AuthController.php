<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService){}
    public function login(AuthRequest $request): JsonResponse
    {
        $data = $this->authService->login($request->validated());
    
        return response()->json($data);
    }
}
