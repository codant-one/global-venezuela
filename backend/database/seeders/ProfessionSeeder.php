<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Profession;

class ProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $professions = [ 
            'Médico General',
            'Enfermero/a',
            'Odontólogo/a',
            'Farmacéutico/a',
            'Psicólogo/a',
            'Nutricionista',
            'Fisioterapeuta',
            'Área de Ingeniería y Tecnología',
            'Ingeniero Civil',
            'Ingeniero de Sistemas',
            'Ingeniero Mecánico',
            'Ingeniero Eléctrico',
            'Ingeniero Industrial',
            'Ingeniero Químico',
            'Arquitecto/a',
            'Área de Ciencias Sociales y Humanidades',
            'Abogado/a',
            'Sociólogo/a',
            'Psicólogo/a',
            'Historiador/a',
            'Antropólogo/a',
            'Periodista',
            'Profesor/a (en diversas especialidades)',
            'Área de Negocios y Finanzas',
            'Administrador/a de Empresas',
            'Contador/a Público/a',
            'Economista',
            'Gerente de Marketing',
            'Analista de Sistemas',
            'Recursos Humanos',
            'Área de Arte y Diseño',
            'Diseñador/a Gráfico/a',
            'Diseñador/a Industrial',
            'Artista Plástico/a',
            'Músico/a',
            'Fotógrafo/a',
            'Escritor/a',
            'Área de Ciencias Naturales y Exactas',
            'Biólogo/a',
            'Químico/a',
            'Matemático/a',
            'Físico/a',
            'Geólogo/a',
            'Otras Profesiones',
            'Chef o Cocinero/a',
            'Piloto/a (Aviación)',
            'Militar (diferentes rangos y especialidades)',
            'Agricultor/a',
            'Marítimo/a (diversas especialidades)',
            'Deportista Profesional (varias disciplinas)'
        ];

        foreach($professions as $profession){
            Profession::create([
                'name' => $profession,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
