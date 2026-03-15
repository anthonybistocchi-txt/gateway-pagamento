<?php

namespace App\Services\Gateways;

use App\Interfaces\PaymentRepositoryGatewayInterface;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;

class HeaderAuthGatewayService implements PaymentRepositoryGatewayInterface
{
    public function processPayment(Transaction $transaction, array $paymentData): bool | array
    {
        $client = $transaction->client;
    
        $baseUrl = config('services.gateway_header.url');

        $response = Http::withHeaders([
            'Gateway-Auth-Token'  => config('services.gateway_header.token'),
            'Gateway-Auth-Secret' => config('services.gateway_header.secret'),
        ])->post("{$baseUrl}/transacoes", [
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
        $baseUrl = config('services.gateway_header.url');

        $response = Http::withHeaders([
            'Gateway-Auth-Token'  => config('services.gateway_header.token'),
            'Gateway-Auth-Secret' => config('services.gateway_header.secret'),
        ])->post("{$baseUrl}/transacoes/reembolso", [
            'id' => $transaction->external_id 
        ]);

        return $response->successful();
    }
}