<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

use App\Models\State;
use App\Models\Municipality;
use App\Imports\MunicipalityImport;
use Maatwebsite\Excel\Facades\Excel;

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
                'name' => mb_strtoupper($municipality['municipio'], 'UTF-8')
            ]);
        }

        $states = State::all();

        foreach($states as $state){

            $originals = 'ÁÉÍÓÚÀÈÌÒÙÄËÏÖÜÂÊÎÔÛáéíóúàèìòùäëïöüâêîôûçÇ';
            $modifieds = 'AEIOUAEIOUAEIOUAEIOUaeiouaeiouaeiouaeioucC';

            $state_name =  Str::slug($state->name);
            $state_decode = utf8_decode($state_name); // Decodifica la cadena a ISO-8859-1
            $text = strtr($state_decode, utf8_decode($originals), $modifieds);
            $name = utf8_encode($text);

            if($name !== 'caracas') {
                Excel::import(
                    new MunicipalityImport, 
                    Storage::disk('local')->path('/excel/estados/'.str_replace('-', '', $name).'.xlsx')
                );
            }
        }
    }
}
