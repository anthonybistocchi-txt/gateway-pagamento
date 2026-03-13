<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Collection;

class UserService
{
    public function __construct(protected UserRepository $userRepository){}

    public function getUsers(): Collection
    {
        return $this->userRepository->getUsers();
    }

    public function getUsersById(array $id): Collection
    {
        return $this->userRepository->getUsersById($id);
    }

    public function storeUser($data): bool
    {
        return $this->userRepository->storeUser($data);
    }

    public function updateUser($id, $data): bool
    {
        return $this->userRepository->updateUser($id, $data);
    }

    // Lógica para deletar um usuário
    public function deleteUser($id): bool
    {
        return $this->userRepository->deleteUser($id);
    }
}