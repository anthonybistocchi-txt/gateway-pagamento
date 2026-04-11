<?php

namespace Tests\Feature\Transaction;

use App\Models\Client;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Validation tests: ensure that invalid inputs never reach the payment service.
 */
class TransactionStoreValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_requires_payment_key(): void
    {
        $client  = Client::factory()->create();
        $product = Product::factory()->create();

        $response = $this->postJson('/api/purchases', [
            'card_number' => '1234567812345678',
            'name' => 'Test',
            'email' => 'test@example.com',
            'quantity' => 1,
            'client_id' => $client->id,
            'product_id' => $product->id,
            'payment_method' => 'card_credit',
            'cvv' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('messages.payment_key.0', 'payment_key is required for idempotent requests.');
    }

    public function test_store_rejects_invalid_payment_key_format(): void
    {
        $client  = Client::factory()->create();
        $product = Product::factory()->create();

        $response = $this->postJson('/api/purchases', [
            'payment_key'    => 'not-a-uuid',
            'card_number'    => '1234567812345678',
            'name'           => 'Test',
            'email'          => 'test@example.com',
            'quantity'       => 1,
            'client_id'      => $client->id,
            'product_id'     => $product->id,
            'payment_method' => 'card_credit',
            'cvv'            => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('messages.payment_key.0', 'payment_key must be a valid UUID.');
    }

    public function test_store_rejects_nonexistent_client_id(): void
    {
        $product = Product::factory()->create();

        $response = $this->postJson('/api/purchases', [
            'payment_key'    => (string) Str::uuid(), 
            'card_number'    => '1234567812345678',
            'name'           => 'Test',
            'email'          => 'test@example.com', 
            'quantity'       => 1,
            'client_id'      => 99999, 
            'product_id'     => $product->id,
            'payment_method' => 'card_credit',
            'cvv'            => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('messages.client_id.0', 'client ID does not exist.');
    }
}
