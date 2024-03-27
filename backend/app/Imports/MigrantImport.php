<?php

namespace App\Imports;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\Models\Migrant;
use App\Models\Country;
use App\Models\Municipality;
use App\Models\Parish;
use App\Models\CommunityCouncil;
use App\Models\Circuit;

class MigrantImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        mb_internal_encoding('UTF-8');

        $country = Country::select('id')->where('name', 'LIKE', '%'.$row[0].'%')->get()->toArray();
        $gender_id = ($row[1] === 'F') ? 1 : 2;
        $state_id = 14; //miranda
        $municipalities = Municipality::where('state_id', $state_id)->get();
        $municipalities_arr = [];

        foreach($municipalities as $municipality){
            $aux = [];
            $aux['id'] = $municipality->id;
            $aux['name'] = Str::slug($municipality->name);
        
            array_push($municipalities_arr, $aux);
        }

        $key = array_search(Str::slug($row[3]), array_column($municipalities_arr, 'name')); 
        $municipality_id = $municipalities_arr[$key]['id'];

        $parishes = Parish::where('municipality_id', $municipality_id)->get();
        $parishes_arr = [];

        foreach($parishes as $parish){
            $aux = [];
            $aux['id'] = $parish->id;
            $aux['name'] = Str::slug($parish->name);
        
            array_push($parishes_arr, $aux);
        }

        $key = array_search(Str::slug($row[4]), array_column($parishes_arr, 'name')); 
        $parish_id = $parishes_arr[$key]['id'];
        $unixDate = ($row[9] - 25569) * 86400; // 25569 es el número de días desde la fecha base de Excel hasta la fecha base de Unix (1 de enero de 1970)
        $date = gmdate('Y-m-d', $unixDate);

        return new Migrant([
            'country_id' => $country[0]['id'],
            'user_id' => 1,
            'gender_id' => $gender_id,
            'parish_id' => $parish_id,
            'community_council_id' => null,
            'name' => mb_convert_case(mb_strtolower($row[6]), MB_CASE_TITLE, "UTF-8"),
            'last_name' => mb_convert_case(mb_strtolower($row[7]), MB_CASE_TITLE, "UTF-8"),
            'email' => $row[8],
            'birthdate' => $date,
            'passport_number' => $row[10],
            'passport_status' => ($row[11] === 'SI') ? 1 : 0,
            'transient' => ($row[12] === 'SI') ? 1 : 0,
            'resident' => ($row[13] === 'SI') ? 1 : 0,
            'process_saime' => ($row[14] === 'SI') ? 1 : 0,
            'years_in_country' => $row[15],
            'antecedents' => ($row[16] === 'SI') ? 1 : 0,
            'isMarried' => ($row[17] === 'SI') ? 1 : 0,
            'has_children' => ($row[18] === 'SI') ? 1 : 0,
            'children_number' => ($row[19] === null || $row[19] === '') ? null : $row[19],
            'phone' => str_replace('-', '', $row['20']) ?? null,
            'address' => $row[21],
            'file_document' => 'migrants/'.$row[22] ?? null
        ]);
    }
}
