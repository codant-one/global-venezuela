<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServerSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            StateSeeder::class,
            CitySeeder::class,
            MunicipalitySeeder::class,
            ParishSeeder::class,
            CircuitSeeder::class,
            CommunityCouncilSeeder::class,

            OperatorSeeder::class,
            VolunteerSeeder::class,

            OperatorPermissionsSeeder::class
        ]);

    }
}
