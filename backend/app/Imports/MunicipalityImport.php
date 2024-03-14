<?php

namespace App\Imports;

use App\Models\State;
use App\Models\Municipality;
use Maatwebsite\Excel\Concerns\ToModel;

class MunicipalityImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        mb_internal_encoding('UTF-8');

        $state = State::select('id')->whereRaw('LOCATE(name, ?) > 0', [$row[0]])->get()->toArray();
        $municipality = 
            Municipality::select('id')
                ->where([
                    ['name', $row[1]],
                    ['state_id', $state[0]['id']]
                ])
                ->get()
                ->toArray();

        if(!$municipality) {
            return new Municipality([
                'state_id' => $state[0]['id'],
                'name' => mb_strtoupper($row[1], 'UTF-8'),
            ]);
        }
    }
}
