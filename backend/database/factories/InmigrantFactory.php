<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Inmigrant;
use App\Models\Country;
use App\Models\CommunityCouncil;
use App\Models\User;
use App\Models\Gender;
use App\Models\Parish;
use App\Models\Circuit;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class InmigrantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Inmigrant::class;

    public function definition(): array
    {
        $has_children = rand(0, 1);
        $parish_id = Parish::InRandomOrder()->first()->id;
        $circuit_id = Circuit::where('parish_id', $parish_id)->InRandomOrder()->first()->id ?? 1;
        $community_council_id = CommunityCouncil::where('circuit_id', $circuit_id)->InRandomOrder()->first()->id ?? 1;

        return [
            'country_id' => Country::InRandomOrder()->first()->id,
            'user_id' => User::InRandomOrder()->first()->id,
            'gender_id' => Gender::InRandomOrder()->first()->id,
            'parish_id' => $parish_id,
            'community_council_id' => rand(0, 1) ? $community_council_id : null,
            'name' => $this->faker->name,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->email,
            'birthdate' => $this->faker->date,
            'passport_number' => strval(rand(1,22999999)),
            'transient' => rand(0, 1),
            'resident' => rand(0, 1),
            'years_in_country' => rand(0, 80),
            'antecedents' => rand(0, 1),
            'isMarried' => rand(0, 1),
            'has_children' => $has_children,
            'children_number' => $has_children ? rand(1, 5) : 0,
            'created_at' => now(),
            'updated_at' => now()
        ];

    }
}
