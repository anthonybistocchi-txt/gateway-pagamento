<?php

namespace App\Services;

use App\Interfaces\PaymentRepositoryGatewayInterface;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;


class Gateway1Service implements PaymentRepositoryGatewayInterface
{
    public function processPayment(Transaction $transaction): bool
    {
        $token = $this->authenticate();

        $response = Http::withToken($token)
            ->post('http://gateways-mock:3001/transactions', [
                'amount'     => $transaction->amount,
                'name'       => 'Cliente BeTalent',      // $transaction->customer->name
                'email'      => 'cliente@betalent.tech', // $transaction->customer->email
                'cardNumber' => '556900000000' . $transaction->card_last_numbers,
                'cvv'        => '010',
            ]);

        if ($response->failed()) 
        {
            return false;
        }

        return true;
    }

    public function refund(Transaction $transaction): bool
    {
        $token = $this->authenticate();

        $response = Http::withToken($token)
            ->post("http://gateways-mock:3001/transactions/{$transaction->external_id}/charge_back");

        return $response->successful();
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