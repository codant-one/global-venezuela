<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CommunityCouncil;
use Carbon\Carbon;
use Str;

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
        $council = rand(1,1000);
        $name = $this->faker->name();

        return [
            'parish_id' => $council,
            'name' => $name,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
