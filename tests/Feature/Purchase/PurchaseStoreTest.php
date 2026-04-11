<?php

namespace Tests\Feature\Purchase;

use App\Models\Client;
use App\Models\Gateway;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class PurchaseStoreTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Feature test: exercises the real HTTP API + database + Http::fake for external gateways.
     * The payment_key is required (idempotence); without it, the validation responds with 422.
     */
    public function test_purchase_store_success_with_valid_data_gateway_priority_1(): void
    {
        $client  = Client::factory()->create(['id' => 1, 'name' => 'Test Client', 'email' => 'test.client@example.com']);
        $product = Product::factory()->create(['id' => 1, 'name' => 'Test Product', 'amount' => 100.00]);

        Gateway::factory()->create(['id' => 1, 'name' => 'gateway_test1', 'is_active' => 1, 'priority' => 1]);
        Gateway::factory()->create(['id' => 2, 'name' => 'gateway_test2', 'is_active' => 1, 'priority' => 2]);

        Http::fake([
            '*/login'   => Http::response([
                'token' => 'token_falso_gerado_no_teste',
            ], 200),
            '*/transactions' => Http::response([
                'id'     => 'EXT_999888777',
                'status' => 'approved',
            ], 200),
        ]);

        $paymentKey = (string) Str::uuid();

        $payload = [
            'payment_key'    => $paymentKey,
            'card_number'    => '1234567812345678',
            'name'           => $client->name,
            'email'          => $client->email,
            'quantity'       => 2,
            'client_id'      => $client->id,
            'product_id'     => $product->id,
            'payment_method' => 'card_credit',
            'cvv'            => '123',
        ];

        $response = $this->postJson('/api/purchases', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status'  => true,
                'message' => 'Transaction created successfully.',
            ]);

        $this->assertDatabaseHas('transactions', [
            'external_id' => 'EXT_999888777',
            'gateway_id'  => 1,
            'status'      => 'completed',
        ]);
    }

    /**
     * When the gateway 1 fails to charge, the service tries the next active by priority.
     */
    public function test_purchase_store_fallback_to_gateway_priority_2_on_gateway_1_failure(): void
    {
        $client  = Client::factory()->create(['id' => 1, 'name' => 'Test Client', 'email' => 'test.client@example.com']);
        $product = Product::factory()->create(['id' => 1, 'name' => 'Test Product', 'amount' => 100.00]);

        Gateway::factory()->create(['id' => 1, 'name' => 'gateway_test1', 'is_active' => 1, 'priority' => 1]);
        Gateway::factory()->create(['id' => 2, 'name' => 'gateway_test2', 'is_active' => 1, 'priority' => 2]);

        Http::fake([
            '*/login'        => Http::response(['token' => 'token_falso_gerado_no_teste'], 200),
            '*/transactions' => Http::response(['error' => 'gateway_unavailable'], 503),
            '*/transacoes'   => Http::response([
                'id'         => 'EXT_FROM_GATEWAY_2',
                'status'     => 'approved',
            ], 200),
        ]);

        $paymentKey = (string) Str::uuid();

        $payload = [
            'payment_key'    => $paymentKey,
            'card_number'    => '1234567812345678',
            'name'           => $client->name,
            'email'          => $client->email,
            'quantity'       => 2,
            'client_id'      => $client->id,
            'product_id'     => $product->id,
            'payment_method' => 'card_credit',
            'cvv'            => '123',
        ];

        $response = $this->postJson('/api/purchases', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status'  => true,
                'message' => 'Transaction created successfully.',
            ]);

        $this->assertDatabaseHas('transactions', [
            'external_id' => 'EXT_FROM_GATEWAY_2',
            'gateway_id'  => 2,
            'status'      => 'completed',
        ]);
    }

    /**
     * Same client_id + payment_key of a completed transaction: idempotent response (200).
     */
    public function test_purchase_store_idempotent_replay_when_transaction_already_completed(): void
    {
        $client  = Client::factory()->create(['id'  => 1, 'name' => 'Test Client', 'email'   => 'test.client@example.com']);
        $product = Product::factory()->create(['id' => 1, 'name' => 'Test Product', 'amount' => 100.00]);

        Gateway::factory()->create(['id' => 1, 'name' => 'gateway_test1', 'is_active' => 1, 'priority' => 1]);
        Gateway::factory()->create(['id' => 2, 'name' => 'gateway_test2', 'is_active' => 1, 'priority' => 2]);

        Http::fake([
            '*/login'        => Http::response(['token' => 'token_falso_gerado_no_teste'], 200),
            '*/transactions' => Http::response([
                'id'         => 'EXT_IDEMPOTENT',
                'status'     => 'approved',
            ], 200),
        ]);

        $paymentKey = (string) Str::uuid();

        $first = $this->postJson('/api/purchases', [
            'payment_key'    => $paymentKey,
            'card_number'    => '1234567812345678',
            'name'           => $client->name,
            'email'          => $client->email,
            'quantity'       => 1,
            'client_id'      => $client->id,
            'product_id'     => $product->id,
            'payment_method' => 'card_credit',
            'cvv'            => '123',
        ]);

        $first->assertStatus(201);

        $second = $this->postJson('/api/purchases', [
            'payment_key'    => $paymentKey,
            'card_number'    => '1234567812345678',
            'name'           => $client->name,
            'email'          => $client->email,
            'quantity'       => 1,
            'client_id'      => $client->id,
            'product_id'     => $product->id,
            'payment_method' => 'card_credit',
            'cvv'            => '123',
        ]);

        $second->assertStatus(200)
            ->assertJson([
                'status'  => true,
                'message' => 'Transaction already completed.',
            ]);
    }

    /**
     * Pending transaction with the same payment_key: conflict (409).
     */
    public function test_purchase_store_returns_conflict_when_pending_transaction_exists_for_payment_key(): void
    {
        $client  = Client::factory()->create(['id' => 1, 'name' => 'Test Client', 'email' => 'test.client@example.com']);
        $product = Product::factory()->create(['id' => 1, 'name' => 'Test Product', 'amount' => 100.00]);

        Gateway::factory()->create(['id' => 1, 'name' => 'gateway_test1', 'is_active' => 1, 'priority' => 1]);
        Gateway::factory()->create(['id' => 2, 'name' => 'gateway_test2', 'is_active' => 1, 'priority' => 2]);

        $paymentKey = (string) Str::uuid();

        Transaction::query()->create([
            'client_id'      => $client->id,
            'gateway_id'     => 1,
            'external_id'    => null,
            'payment_key'    => $paymentKey,
            'status'         => 'pending',
            'amount'         => 100.00,
            'quantity'       => '1',
            'payment_method' => 'card_credit',
            'product_id'     => $product->id,
        ]);

        Http::fake();

        $response = $this->postJson('/api/purchases', [
            'payment_key'    => $paymentKey,
            'card_number'    => '1234567812345678',
            'name'           => $client->name,
            'email'          => $client->email,
            'quantity'       => 1,
            'client_id'      => $client->id,
            'product_id'     => $product->id,
            'payment_method' => 'card_credit',
            'cvv'            => '123',
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'status' => false,
                'message' => 'A transaction with this payment_key is already being processed. Conflict.',
            ]);
    }
}

