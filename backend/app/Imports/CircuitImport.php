<?php

namespace App\Imports;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\Models\Circuit;
use App\Models\State;
use App\Models\Municipality;

class CircuitImport implements ToModel
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

        $municipality_id = ($state[0]['id'] === 24) ? 462 : $municipality[0]['id'];

        $circuit = 
            Circuit::select('name')
                ->where([
                    ['name', $row[2]],
                    ['municipality_id', $municipality_id]
                ])
                ->get()
                ->toArray();

        if(!$circuit) {
            return new Circuit([
                'municipality_id' => $municipality_id,
                'name' => mb_strtoupper($row[2], 'UTF-8')
            ]);    
        }
                                   
    }
}
