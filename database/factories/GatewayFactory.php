<?php

namespace Database\Factories;

use App\Models\Gateway;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gateway>
 */
class GatewayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Gateway::class;
    public function definition(): array
    {
        return [
            'name'      => fake()->word(),
            'is_active' => fake()->boolean(),
            'priority'  => fake()->numberBetween(1, 2),
        ];
    }
}
