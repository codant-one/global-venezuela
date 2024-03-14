<?php

namespace App\Imports;

use App\Models\State;
use App\Models\Municipality;
use App\Models\Circuit;
use App\Models\CommunityCouncil;
use Maatwebsite\Excel\Concerns\ToModel;

class CommunityCouncilImport implements ToModel
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
            Circuit::select('id')
                ->where([
                    ['name', $row[2]],
                    ['municipality_id', $municipality_id]
                ])
                ->get()
                ->toArray();

        $communityCouncil = 
            CommunityCouncil::select('name')
                ->where([
                    ['name', $row[3]],
                    ['circuit_id', $circuit[0]['id']]
                ])
                ->get()
                ->toArray();

        if(!$communityCouncil) {
            return new CommunityCouncil([
                'circuit_id' => $circuit[0]['id'],
                'name' => mb_strtoupper($row[3], 'UTF-8')
            ]);
        }
    }
}
