<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class OtherPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'ver circuitos']);
        Permission::create(['name' => 'eliminar circuitos']);
        Permission::create(['name' => 'crear circuitos']);
        Permission::create(['name' => 'editar circuitos']);

        Permission::create(['name' => 'ver consejos-comunales']);
        Permission::create(['name' => 'eliminar consejos-comunales']);
        Permission::create(['name' => 'crear consejos-comunales']);
        Permission::create(['name' => 'editar consejos-comunales']);

        Permission::create(['name' => 'ver inmigrantes']);
        Permission::create(['name' => 'eliminar inmigrantes']);
        Permission::create(['name' => 'crear inmigrantes']);
        Permission::create(['name' => 'editar inmigrantes']);

        Permission::create(['name' => 'ver reportes']);

    }
}
