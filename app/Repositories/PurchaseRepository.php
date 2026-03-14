<?php

namespace App\Repositories;

use App\Interfaces\PurchaseRepositoryInterface;
use App\Models\Purchase;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

class PurchaseRepository implements PurchaseRepositoryInterface
{
    public function show(): Collection
    {
        return Purchase::all();
    }

    public function details(array $data)
    {
        return Purchase::with(['client', 'product', 'transaction'])
            ->where('id', $data['id'])
            ->first();
    }

    public function store(Transaction $data): bool
    {
        return Purchase::insert([
            'client_id'      => $data['client_id'],
            'product_id'     => $data['product_id'],
            'amount'         => $data['amount'],
            'status'         => 'completed',
            'transaction_id' => $data->id,
        ]);  
    }

    public function updateRefund(array $data): bool
    {
        return Purchase::where('transaction_id', $data['id'])
        ->where('status', 'completed')
        ->update([
            'status' => 'refunded',
        ]);
    }
}