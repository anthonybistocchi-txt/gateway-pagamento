<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Gateway;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin BeTalent',
            'email' => 'admin@betalent.tech',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        Gateway::create([
            'name' => 'Gateway 1',
            'is_active' => true,
            'priority' => 1, 
        ]);

        Gateway::create([
            'name' => 'Gateway 2',
            'is_active' => true,
            'priority' => 2, 
        ]);

        Client::create([
            'name' => 'Cliente Teste',
            'email' => 'cliente@teste.com',
        ]);

        Product::create([
            'name' => 'Teclado Mecânico',
            'amount' => 35000, // R$ 350,00
        ]);

        Product::create([
            'name' => 'Mouse Sem Fio',
            'amount' => 12050, // R$ 120,50
        ]);
    }
}
