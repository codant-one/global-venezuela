<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Circuit;
use App\Models\Parish;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CircuitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Circuit::class;

    public function definition(): array
    {
        return [
            'parish_id' => Parish::InRandomOrder()->first()->id,
            'name' => $this->faker->name,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
