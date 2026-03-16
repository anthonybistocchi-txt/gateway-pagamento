<?php

namespace Tests\Feature\Purchase;

use App\Models\Client;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchaseStore extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase, WithFaker;
    public function test_purchase_store_success(): void
    {
        $client = Client::factory()->create([
            'id'    => 1,
            'name'  => 'Test Client',
            'email' => 'test.client@example.com',
        ]);

        $product = Product::factory()->create([
            'id'     => 1,
            'name'   => 'Test Product',
            'amount' => 100.00,
        ]);

        $payload = [
            'card_number'    => '1234567812345678',
            'name'           => 'John Doe',
            'email'          => 'john.doe@example.com',
            'quantity'       => 2,
            'client_id'      => $client->id,
            'product_id'     => $product->id,
            'payment_method' => 'card_credit',
            'cvv'            => '123',
        ];

        $response = $this->postJson('/api/purchases', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                    'status' => true,
                    'message' => 'Transaction created successfully.',
                ]);
    }
}
