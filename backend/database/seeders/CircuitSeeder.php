<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

use App\Models\State;
use App\Models\Circuit;
use App\Imports\CircuitImport;
use Maatwebsite\Excel\Facades\Excel;

class CircuitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Circuit::factory()->count(50)->create();

        $states = State::all();

        foreach($states as $state){
            $originals = 'ÁÉÍÓÚÀÈÌÒÙÄËÏÖÜÂÊÎÔÛáéíóúàèìòùäëïöüâêîôûçÇ';
            $modifieds = 'AEIOUAEIOUAEIOUAEIOUaeiouaeiouaeiouaeioucC';

            $state_name =  Str::slug($state->name);
            $state_decode = utf8_decode($state_name); // Decodifica la cadena a ISO-8859-1
            $text = strtr($state_decode, utf8_decode($originals), $modifieds);
            $name = utf8_encode($text);

            Excel::import(
                new CircuitImport, 
                Storage::disk('local')->path('/excel/estados/'.str_replace('-', '', $name).'.xlsx')
            );
        }
    }
}
