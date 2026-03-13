<?php

namespace App\Interfaces;
use Illuminate\Support\Collection;
interface UserRepositoryInterface
{
    public function getUsers(): Collection;
    public function getUsersById(array $id): Collection;
    public function storeUser(array $data): bool;
    public function updateUser(int $id, array $data): bool;
    public function deleteUser(int $id): bool;
}