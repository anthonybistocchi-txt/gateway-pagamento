<?php

namespace App\Services\Gateways;

use App\Interfaces\PaymentRepositoryGatewayInterface;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;

class BearerTokenGatewayService implements PaymentRepositoryGatewayInterface
{
    public function processPayment(Transaction $transaction, array $paymentData): bool | array
    {
        $token  = $this->authenticate();
        $client = $transaction->client;
        
        $baseUrl = config('services.gateway_bearer.url');

        $response = Http::withToken($token)
            ->post("{$baseUrl}/transactions", [
                'amount'     => $transaction->amount,
                'name'       => $client->name,
                'email'      => $client->email,
                'cardNumber' => $paymentData['card_number'],
                'cvv'        => $paymentData['cvv'],
            ]);
        
        if ($response->failed()) 
        {
            return false;
        }

        return $response->json();
    }

    public function refund(Transaction $transaction): bool
    {
        $token = $this->authenticate();
        $baseUrl = config('services.gateway_bearer.url');

        $response = Http::withToken($token)
            ->post("{$baseUrl}/transactions/{$transaction->external_id}/charge_back");

        return $response->successful();
    }

    private function authenticate(): string 
    {
        $baseUrl = config('services.gateway_bearer.url');

        $response = Http::post("{$baseUrl}/login", [
            'email' => config('services.gateway_bearer.email'),
            'token' => config('services.gateway_bearer.token')
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to authenticate with Gateway 1.');
        }

        return $response->json('token');
    }
}