<?php

namespace App\Interfaces;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface PurchaseRepositoryInterface
{
    public function show(): Collection;
    public function store(Transaction $data): bool;
    public function details(array $data);
    public function updateRefund(array $data): bool;
}