<?php

namespace App\Interfaces;

use App\Models\Purchase;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface PurchaseRepositoryInterface
{
    public function show(): Collection;
    public function store(Transaction $data): bool;
    public function details(array $data): ?Purchase;
    public function updateRefund(int $id): bool;
}