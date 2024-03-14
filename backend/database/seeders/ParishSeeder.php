<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

use App\Models\Parish;

class ParishSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json_info = Storage::disk('local')->get('/json/parishes.json');
        $parishes_info = json_decode($json_info, true);

        foreach($parishes_info as $parish){
            Parish::query()->updateOrCreate([
                'id' => $parish['id_parroquia'],
                'municipality_id' => $parish['id_municipio'],
                'name' => mb_strtoupper($parish['parroquia'], 'UTF-8')
            ]);
        }
    }
}
