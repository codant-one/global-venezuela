<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

use App\Models\Migrant;
use App\Imports\MigrantImport;
use Maatwebsite\Excel\Facades\Excel;

class MigrantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Migrant::factory()->count(1000)->create();

        Excel::import(new MigrantImport, Storage::disk('local')->path('/excel/migrants.xlsx'));
    }
}
