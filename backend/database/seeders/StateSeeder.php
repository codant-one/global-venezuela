<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

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
                'name' => ucfirst(mb_strtolower($state['estado'])),
                'iso_3166-2' => $state['iso_3166-2']
            ]);
        }
    }
}
