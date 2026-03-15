<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserIdRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(protected UserService $userService){}

    public function index(): JsonResponse
    {
        $data = $this->userService->getUsers();

        return response()->json([
            'status'  => true,
            'message' => 'Users retrieved successfully',
            'data'    => $data
        ]);
    }
    
    public function show(UserIdRequest $request): JsonResponse
    {
        $data = $this->userService->getUsersById($request->validated()); // no route for this method, but can be used in the future if needed

        return response()->json([
            'status'  => true,
            'message' => 'User retrieved successfully',
            'data'    => $data
        ]);
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $this->userService->storeUser($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'User created successfully',
        ]);
    }

    public function update(UserUpdateRequest $request): JsonResponse
    {
        $this->userService->updateUser($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'User updated successfully',
        ]);
    }

    public function destroy(UserIdRequest $request): JsonResponse
    {
        $this->userService->deleteUser($request->id);

        return response()->json([
            'status'  => true,
            'message' => 'User deleted successfully',
        ]);
    }
}
