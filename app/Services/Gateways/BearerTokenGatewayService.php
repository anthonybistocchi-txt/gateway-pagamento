<?php

namespace App\Services\Gateways;

use App\Interfaces\PaymentRepositoryGatewayInterface;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;


class BearerTokenGatewayService implements PaymentRepositoryGatewayInterface
{
    public function __construct(protected HeaderAuthGatewayService $headerAuthGatewayService){}
    public function processPayment(Transaction $transaction, array $paymentData): bool | array
    {
        $token = $this->authenticate();
        
        $client = $transaction->client;

        $response = Http::withToken($token)
            ->post('http://gateways-mock:3001/transactions', [
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

        $response = Http::withToken($token)
            ->post("http://gateways-mock:3001/transactions/{$transaction->external_id}/charge_back");

        return $response->successful();
    }

    public function updatePriority(int $gatewayId, int $priority): void
    {
        
    }

    private function authenticate(): string 
    {
        $response = Http::post('http://gateways-mock:3001/login', [
            'email' => 'dev@betalent.tech',
            'token' => 'FEC9BB078BF338F464F96B48089EB498'
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to authenticate with Gateway 1.');
        }

        return $response->json('token');
    }
}