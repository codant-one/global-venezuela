<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

use App\Models\State;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json_info = Storage::disk('local')->get('/json/states.json');
        $states_info = json_decode($json_info, true);

        foreach($states_info as $state){
            State::query()->updateOrCreate([
                'id' => $state['id_estado'],
                'country_id' => 231,
                'name' => mb_strtoupper($state['estado'], 'UTF-8'),
                'iso_3166-2' => $state['iso_3166-2']
            ]);
        }
    }
}
