<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

use App\Models\Volunteer;
use App\Imports\VolunteerImport;
use Maatwebsite\Excel\Facades\Excel;

class VolunteerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Excel::import(new VolunteerImport, Storage::disk('local')->path('/excel/voluntareado2024.xlsx'));
    }
}
