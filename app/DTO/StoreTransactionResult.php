<?php

namespace App\DTO;

use App\Models\Transaction;

final readonly class StoreTransactionResult
{
    public function __construct(
        public int   $httpStatus,
        public array $payload,
    ) {}

    public static function created(Transaction $transaction): self
    {
        return new self(201, [
            'status'             => true,
            'message'            => 'Transaction created successfully.',
            'transaction_id'     => $transaction->uuid,
            'transaction_status' => $transaction->status,
        ]);
    }

    public static function idempotentReplay(Transaction $transaction): self
    {
        $message = match ($transaction->status) 
        {
            'completed' => 'Transaction already completed.',
            'failed'    => 'Transaction previously failed.',
            'refunded'  => 'Transaction was refunded.',
            default     => 'Transaction already processed.',
        };

        return new self(200, [
            'status'             => true,
            'message'            => $message,
            'transaction_id'     => $transaction->uuid,
            'transaction_status' => $transaction->status,
        ]);
    }

    public static function pendingConflict(Transaction $transaction): self
    {
        return new self(409, [
            'status'             => false,
            'message'            => 'A transaction with this payment_key is already being processed. Conflict.',
            'transaction_id'     => $transaction->id,
            'transaction_status' => $transaction->status,
        ]);
    }
}
