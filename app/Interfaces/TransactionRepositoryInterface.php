<?php
namespace App\Interfaces;

use App\Models\Transaction;

interface TransactionRepositoryInterface
{
    public function pendingTransaction(array $requestData): Transaction;
    public function findByClientAndPaymentKey(int $clientId, string $paymentKey): ?Transaction;
    public function successTransaction(Transaction $transaction): bool;
    public function failedTransaction(Transaction $transaction): bool;
    public function refundTransaction(Transaction $transaction): bool;
    public function find(int $id): Transaction;
}