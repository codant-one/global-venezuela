<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Gender;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $genders = [ 
            [
                'name' => 'Femenino',
                'code' => 'F',
            ],
            [
                'name' => 'Masculino',
                'code' => 'M',
            ]
        ];

        Gender::insert($genders);
    }
}
