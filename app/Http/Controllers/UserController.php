<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserByIdRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(protected UserService $userService){}

    public function get()
    {
        $data = $this->userService->getUsers();

        return response()->json([
            'status'  => true,
            'message' => 'Users retrieved successfully',
            'data'    => $data
        ]);
    }
    
    public function getById(UserByIdRequest $request)
    {
        $data = $this->userService->getUsersById($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'User retrieved successfully',
            'data'    => $data
        ]);
    }

    public function store(UserCreateRequest $request)
    {
        $this->userService->storeUser($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'User created successfully',
        ]);
    }

    public function update(UserByIdRequest $request)
    {
        $this->userService->updateUser($request->id, $request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'User updated successfully',
        ]);
    }

    public function delete($id)
    {
        $this->userService->deleteUser($id);

        return response()->json([
            'status'  => true,
            'message' => 'User deleted successfully',
        ]);
    }
}
