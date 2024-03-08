<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json_info = Storage::disk('local')->get('/json/cities.json');
        $cities_info = json_decode($json_info, true);

        foreach($cities_info as $city){
            City::query()->updateOrCreate([
                'id' => $city['id_ciudad'],
                'state_id' => $city['id_estado'],
                'name' => ucfirst(mb_strtolower($city['ciudad'])),
                'capital' => $city['capital']
            ]);
        }
    }
}
