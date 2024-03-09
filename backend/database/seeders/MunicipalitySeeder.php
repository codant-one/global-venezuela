<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

use App\Models\Municipality;

class MunicipalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json_info = Storage::disk('local')->get('/json/municipalities.json');
        $municipalities_info = json_decode($json_info, true);

        foreach($municipalities_info as $municipality){
            Municipality::query()->updateOrCreate([
                'id' => $municipality['id_municipio'],
                'state_id' => $municipality['id_estado'],
                'name' => ucfirst(mb_strtolower($municipality['municipio']))
            ]);
        }
    }
}
