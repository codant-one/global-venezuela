<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\InstructionDegree;

class InstructionDegreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $instructionDegree = [ 
            'Básica',
            'Secundaria',
            'Universitaria',
            'Profesional',
            'TSU',
            'Doctor',
            'Magister'
        ];

        foreach($instructionDegree as $value){
            InstructionDegree::create([
                'name' => $value,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
