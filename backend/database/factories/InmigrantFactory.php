<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Inmigrant;
use Carbon\Carbon;
use Str;
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
        $country = rand(1,200);
        $council = rand(1,20);
        $user = 1;
        $gender = rand(1,2);
        $name = $this->faker->name();
        $last_name = $this->faker->name();
        $birthdate = $this->faker->date;
        $passport = rand(1,1000);
        $transient = rand(0,1);
        $resident = rand(0,1);
        $years_country = rand(0,80);
        $antecedents = rand(0,1);
        $married = rand(0,1);
        $has_children = rand(0,1);
        if($has_children>0)
        {
            $numbre_children = rand(1,10);
        }
        else
        {
            $numbre_children = 0;
        }

        return [
            'country_id' => $country,
            'community_council_id'=> $council,
            'user_id'=> $user,
            'gender_id'=> $gender,
            'name' => $name,
            'last_name'=> $last_name,
            'birthdate'=> $birthdate,
            'passport_number'=> $passport,
            'transient'=> $transient,
            'resident'=> $resident,
            'years_in_country'=> $years_country,
            'antecedents'=> $antecedents,
            'married_venezuela'=> $married,
            'has_venezuelan_children'=> $has_children,
            'number_of_children'=> $numbre_children,
            'created_at' => now(),
            'updated_at' => now()
        ];

    }
}
