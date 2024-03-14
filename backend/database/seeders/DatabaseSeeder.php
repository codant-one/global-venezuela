<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            PermissionSeeder::class,
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            MunicipalitySeeder::class,
            ParishSeeder::class,
            AdminSeeder::class,
            OperatorSeeder::class,

            GenderSeeder::class,

            CircuitSeeder::class,
            CommunityCouncilSeeder::class,
            MigrantSeeder::class,
            ThemeSeeder::class,

            OtherPermissionsSeeder::class,
            VolunteerSeeder::class,
            VolunteerPermissionsSeeder::class,

            OperatorPermissionsSeeder::class

        ]);

    }
}
