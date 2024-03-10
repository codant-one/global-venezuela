<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Theme;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Array de nombres para los elementos
        $names = [
                    'Economía del Futuro', 
                    'Venezuela Abierta al Futuro', 
                    'Verdad y Paz Nacional', 
                    'Venezuela Comunitaria en Movimiento', 
                    'Valores para el Futuro', 
                    'Ciudades del Futuro', 
                    'Venezuela Global'
                ];

        // Iterar sobre los nombres y crear un registro en la base de datos para cada uno
        foreach ($names as $name) {
            Theme::create([
                'name' => $name
            ]);
        }
    }
}
