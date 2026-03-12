<?php

namespace App\Interfaces;

use App\Models\Transaction;

interface PaymentRepositoryGatewayInterface
{
    public function processPayment(Transaction $transaction);

    public function refund(Transaction $transaction): bool;

}