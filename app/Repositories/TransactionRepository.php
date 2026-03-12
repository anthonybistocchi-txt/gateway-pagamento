<?php

namespace App\Repositories;

use App\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;

class TransactionRepository implements TransactionRepositoryInterface
{
   public function pendingTransaction(array $requestData): Transaction
    {
        return Transaction::create([
            'client_id'         => $requestData['client_id'],
            'payment_method'    => $requestData['payment_method'] ?? null,
            'product_id'        => $requestData['product_id'],
            'amount'            => $requestData['amount'],
            'gateway_id'        => $requestData['gateway_id'] ?? 1,
            'external_id'       => $requestData['external_id'] ?? null,
            'card_last_numbers' => $requestData['card_last_numbers'] ?? null,
            'status'            => 'pending',
        ]);
    }

    public function successTransaction(Transaction $transaction, int $gatewayId): bool
    {   
        return $transaction->update([
            'status'     => 'completed',
            'gateway_id' => $gatewayId
        ]);
    }

    public function failedTransaction(Transaction $transaction): bool
    {
        return $transaction->update(['status' => 'failed']);
    }

    public function refundTransaction(Transaction $transaction): bool
    {
        return $transaction->update(['status' => 'refunded']);
    }
}