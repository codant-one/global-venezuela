<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

use App\Models\City;

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
                'name' => mb_strtoupper($city['ciudad'], 'UTF-8'),
                'capital' => $city['capital']
            ]);
        }
    }
}
