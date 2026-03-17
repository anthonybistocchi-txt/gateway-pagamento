<?php

namespace Tests\Feature\Purchase;

use App\Models\Client;
use App\Models\Gateway;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PurchaseStoreTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase, WithFaker;
    public function test_purchase_store_success_with_valid_data_gateway_priority_1(): void
    {
        $client  = Client::factory()->create(['id' => 1,'name' => 'Test Client','email' => 'test.client@example.com']);
        $product = Product::factory()->create(['id' => 1,'name'   => 'Test Product','amount' => 100.00]);

        Gateway::factory()->create(['id' => 1,'name' => 'gateway_test1','is_active' => 1,'priority'  => 1]);
        Gateway::factory()->create(['id' => 2,'name' => 'gateway_test2','is_active' => 1,'priority'  => 2]);

        Http::fake([
            '*/login'   => Http::response([
                'token' => 'token_falso_gerado_no_teste'
            ], 200),

            '*/transactions' => Http::response([
                'id'     => 'EXT_999888777', 
                'status' => 'approved'
            ], 200),
         ]);

        $payload = [
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
                    'status' => true,
                    'message' => 'Transaction created successfully.',
                ]);

        $this->assertDatabaseHas('transactions', [
            'external_id' => 'EXT_999888777',
            'gateway_id'  => 1,
            'status'      => 'completed'
        ]);
    }

    // public function test_purchase_store_fallback_to_gateway_priority_2_on_gateway_1_failure(): void
    // {
    //     $client  = Client::factory()->create(['id' => 1,'name' => 'Test Client','email' => 'test.client@example.com']);
    //     $product = Product::factory()->create(['id' => 1,'name'   => 'Test Product','amount' => 100.00]);

    //     Gateway::factory()->create(['id' => 1,'name' => 'gateway_test1','is_active' => 1,'priority'  => 1]);
    //     Gateway::factory()->create(['id' => 2,'name' => 'gateway_test2','is_active' => 1,'priority'  => 2]);

    //     Http::fake([
    //         '*/login'   => Http::response([
    //             'token' => 'token_falso_gerado_no_teste'
    //         ], 200),

    //         '*/transactions' => Http::response([
    //             'id'     => 'EXT_999888777', 
    //             'status' => 'approved'
    //         ], 200),
    //      ]);

    //     $payload = [
    //         'card_number'    => '1234567812345678',
    //         'name'           => $client->name,
    //         'email'          => $client->email,
    //         'quantity'       => 2,
    //         'client_id'      => $client->id,
    //         'product_id'     => $product->id,
    //         'payment_method' => 'card_credit',
    //         'cvv'            => '123',
    //     ];

    //     $response = $this->postJson('/api/purchases', $payload);

    //     $response->assertStatus(201)
    //             ->assertJson([
    //                 'status' => true,
    //                 'message' => 'Transaction created successfully.',
    //             ]);

    //     $this->assertDatabaseHas('transactions', [
    //         'external_id' => 'EXT_999888777',
    //         'gateway_id'  => 2,
    //         'status'      => 'completed'
    //     ]);
    // }
}

