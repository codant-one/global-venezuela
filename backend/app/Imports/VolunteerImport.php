<?php

namespace App\Imports;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\Models\Volunteer;
use App\Models\State;
use App\Models\Municipality;

class VolunteerImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        mb_internal_encoding('UTF-8');
        preg_match('/\d+/', $row[3], $theme_id);

        $states = State::all();
        $states_arr = [];

        foreach($states as $state){
            $aux = [];
            $aux['id'] = $state->id;
            $aux['name'] = Str::slug($state->name);
        
            array_push($states_arr, $aux);
        }

        $key = array_search(Str::slug($row[4]), array_column($states_arr, 'name')); 


        if($row[5] === 'ESTADAL') {//busca estado
            $state_id = $states_arr[$key]['id'];
            $municipality_id = null;
        } else {// busca municipio
            $state_id = null;
            $municipalities = Municipality::where('state_id', $states_arr[$key]['id'])->get();
            $municipalities_arr = [];

            foreach($municipalities as $municipality){
                $aux = [];
                $aux['id'] = $municipality->id;
                $aux['name'] = Str::slug($municipality->name);
            
                array_push($municipalities_arr, $aux);
            }

            $key = array_search(Str::slug($row[5]), array_column($municipalities_arr, 'name')); 

            $municipality_id = $municipalities_arr[$key]['id'];
        }
      
        $isResponsible = str_contains($row[3], 'COORDINADOR') ? 1 : 0; 

        return new Volunteer([
            'theme_id' => $theme_id[0],
            'state_id' => $state_id,
            'municipality_id' => $municipality_id,
            'circuit_id' => null,
            'isResponsible' => $isResponsible,
            'name' => mb_convert_case(mb_strtolower($row[0]), MB_CASE_TITLE, "UTF-8"),
            'document' => str_replace('.', '', $row['1']) ?? null,
            'phone' => str_replace('-', '', $row['2']) ?? null,
            'email' => null
        ]);
    }
}
