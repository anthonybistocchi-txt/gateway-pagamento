<?php

namespace App\DTO;

final readonly class RefundTransactionResult
{
    public function __construct(
        public int $httpStatus,
        public array $payload,
    ) {}

    public static function success(): self
    {
        return new self(200, [
            'status'  => true,
            'message' => 'Refund processed successfully.',
        ]);
    }
}
