<?php

namespace App\Services;

use App\Interfaces\PaymentRepositoryGatewayInterface;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;

class Gateway2Service implements PaymentRepositoryGatewayInterface
{
    public function processPayment(Transaction $transaction, array $paymentData): bool | array
    {
        $client = $transaction->client;

        $response = Http::withHeaders([
            'Gateway-Auth-Token'  => env('GATEWAY_AUTH_TOKEN'),
            'Gateway-Auth-Secret' => env('GATEWAY_AUTH_SECRET')

        ])->post('http://gateways-mock:3002/transacoes', [
            'valor'        => $transaction->amount,
            'nome'         => $client->name,
            'email'        => $client->email,
            'numeroCartao' => $paymentData['card_number'],
            'cvv'          => $paymentData['cvv'],
        ]);

        if ($response->failed()) 
        {
            return false;
        }
 
        return $response->json();
    }

    public function refund(Transaction $transaction): bool
    {
        $response = Http::withHeaders([
            'Gateway-Auth-Token'  => env('GATEWAY_AUTH_TOKEN'),
            'Gateway-Auth-Secret' => env('GATEWAY_AUTH_SECRET')                     // testar

        ])->post('http://gateways-mock:3002/transacoes/reembolso', [
            'id' => $transaction->external_id 
        ]);

        return $response->successful();
    }
}