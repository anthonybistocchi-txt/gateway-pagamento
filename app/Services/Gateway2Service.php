<?php

namespace App\Services;

use App\Interfaces\PaymentRepositoryGatewayInterface;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;

class Gateway2Service implements PaymentRepositoryGatewayInterface
{
    public function processPayment(Transaction $transaction): bool
    {
        $response = Http::withHeaders([
            'Gateway-Auth-Token'  => env('GATEWAY_AUTH_TOKEN'),
            'Gateway-Auth-Secret' => env('GATEWAY_AUTH_SECRET')

        ])->post('http://gateways-mock:3002/transacoes', [
            'valor'        => $transaction->amount,
            'nome'         => 'Cliente BeTalent',
            'email'        => 'cliente@betalent.tech',
            'numeroCartao' => '556900000000' . $transaction->card_last_numbers,
            'cvv'          => '010',
        ]);

        if ($response->failed()) 
        {
            return false;
        }

        return true;
    }

    public function refund(Transaction $transaction): bool
    {
        $response = Http::withHeaders([
            'Gateway-Auth-Token'  => env('GATEWAY_AUTH_TOKEN'),
            'Gateway-Auth-Secret' => env('GATEWAY_AUTH_SECRET')

        ])->post('http://gateways-mock:3002/transacoes/reembolso', [
            'id' => $transaction->external_id 
        ]);

        return $response->successful();
    }
}