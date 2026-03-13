<?php

namespace App\Repositories;

use App\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;

class TransactionRepository implements TransactionRepositoryInterface
{
   public function pendingTransaction(array $requestData): Transaction
    {
        return Transaction::create([
            'quantity'       => $requestData['quantity'],
            'client_id'      => $requestData['client_id'],
            'payment_method' => $requestData['payment_method'],
            'product_id'     => $requestData['product_id'],
            'amount'         => (int)$requestData['amount'],
            'gateway_id'     =>  $requestData['gateway_id'] ?? 1, 
            'external_id'    =>  null,
            'cvv'            => $requestData['cvv'],
            'status'         => 'pending',
        ]);
    }

    public function successTransaction(Transaction $transaction): bool
    {
        return $transaction->update([
            'status'      => 'completed',
            'gateway_id'  => $transaction->gateway_id,
            'external_id' => $transaction->external_id
        ]);
    }

    public function failedTransaction(Transaction $transaction): bool
    {
        return $transaction->update([
            'status'      => 'failed',
            'gateway_id'  => $transaction->gateway_id  ?? null,
            'external_id' => $transaction->external_id ?? null
        ]);
    }

    public function refundTransaction(Transaction $transaction): bool
    {
        return $transaction->update([
            'status'      => 'refunded',
            'gateway_id'  => $transaction->gateway_id  ?? null,
        ]);
    }

    public function find($id): Transaction
    {
        return Transaction::findOrFail($id);
    }
}