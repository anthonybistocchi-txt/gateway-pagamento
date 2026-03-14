<?php

namespace App\Services;

use App\Interfaces\PurchaseRepositoryInterface;

class PurchaseService
{
    public function __construct(protected PurchaseRepositoryInterface $repository){}

    public function details(array $data)
    {
        return $this->repository->details($data);
    }

    public function getAll()
    {
        return $this->repository->show();
    }
}