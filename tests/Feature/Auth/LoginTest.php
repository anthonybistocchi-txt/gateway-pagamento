<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
    */
    use RefreshDatabase;

    public function test_user_can_log_in_with_valid_credentials()
    {
        Role::create(['name' => 'ADMIN']);

        User::factory()->create([
            'name'     => 'Anthony Bistocchi',
            'email'    => 'anthony@gmail.com',
            'password' => Hash::make('password123'),
            'role_id'  => 1,
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'anthony@gmail.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure(['token']);            
    }
    
    public function test_user_cannot_log_in_with_invalid_credentials()
    {
        Role::create(['name' => 'ADMIN']);

        User::factory()->create([
            'name'     => 'Anthony Bistocchi',
            'email'    => 'anthony@gmail.com',
            'password' => Hash::make('password123'),
            'role_id'  => 1,
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'anthony@gmail.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401);

        $response->assertJson([
            'error' => 'invalid credentials'
        ]);
    }

    public function test_user_cannot_log_in_with_non_existent_email()
    {
        Role::create(['name' => 'ADMIN']);

        User::factory()->create([
            'name'     => 'Anthony Bistocchi',
            'email'    => 'anthony@gmail.com',
            'password' => Hash::make('password123'),
            'role_id'  => 1,
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'nonexistent@gmail.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(401);

        $response->assertJson([
            'error' => 'invalid credentials'
        ]);
    }
}