<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\CommunityCouncil;
use App\Models\Circuit;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CommunityCouncilFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = CommunityCouncil::class;

    public function definition(): array
    {
        return [
            'circuit_id' => Circuit::InRandomOrder()->first()->id,
            'name' => $this->faker->name,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
