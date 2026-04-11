<?php

namespace Tests\Unit;

use App\DTO\StoreTransactionResult;
use App\Models\Transaction;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests: a standalone class, without Laravel (no HTTP, no database).
 * Useful to learn how to verify pure behavior (return values).
 */
class StoreTransactionResultTest extends TestCase
{
    public function test_created_carries_201_and_success_payload(): void
    {
        $transaction = new Transaction;
        $transaction->setAttribute('uuid', '550e8400-e29b-41d4-a716-446655440000');
        $transaction->setAttribute('status', 'completed');

        $result = StoreTransactionResult::created($transaction);

        $this->assertSame(201, $result->httpStatus);
        $this->assertTrue($result->payload['status']);
        $this->assertSame('Transaction created successfully.', $result->payload['message']);
        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $result->payload['transaction_id']);
        $this->assertSame('completed', $result->payload['transaction_status']);
    }

    public function test_pending_conflict_returns_409(): void
    {
        $transaction = new Transaction;
        $transaction->setAttribute('id', 42);
        $transaction->setAttribute('status', 'pending');

        $result = StoreTransactionResult::pendingConflict($transaction);

        $this->assertSame(409, $result->httpStatus);
        $this->assertFalse($result->payload['status']);
        $this->assertSame(42, $result->payload['transaction_id']);
    }

    public function test_idempotent_replay_message_matches_completed_status(): void
    {
        $transaction = new Transaction;
        $transaction->setAttribute('uuid', 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee');
        $transaction->setAttribute('status', 'completed');

        $result = StoreTransactionResult::idempotentReplay($transaction);

        $this->assertSame(200, $result->httpStatus);
        $this->assertSame('Transaction already completed.', $result->payload['message']);
    }
}
