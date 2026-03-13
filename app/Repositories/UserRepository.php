<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;


class UserRepository implements UserRepositoryInterface
{
    public function getUsers(): Collection
    {
        return User::all();
    }

    public function getUsersById(array $id): Collection
    {
        return User::whereIn('id', $id)->get();
    }

    public function storeUser(array $data): bool
    {
       return User::create($data) ? true : false;
    }

    public function updateUser(int $id, array $data): bool
    {
        $user = User::find($id);

        if (!$user) {
            return false;
        }

        return $user->update($data) ? true : false;
    }

    public function deleteUser(int $id): bool
    {
        $user = User::find($id);

        if (!$user) {
            return false;
        }

        return $user->delete() ? true : false;
    }
}