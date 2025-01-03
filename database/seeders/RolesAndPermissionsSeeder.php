<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Eliminar caché de permisos y roles
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        Permission::create(['name' => 'ver usuarios']);
        Permission::create(['name' => 'crear usuarios']);
        Permission::create(['name' => 'editar usuarios']);
        Permission::create(['name' => 'eliminar usuarios']);

        Permission::create(['name' => 'ver carga']);
        Permission::create(['name' => 'crear carga']);
        Permission::create(['name' => 'editar carga']);
        Permission::create(['name' => 'eliminar carga']);

        // Crear roles y asignar permisos
        $master = Role::create(['name' => 'Master']);
        $master->givePermissionTo(Permission::all());

        $customer = Role::create(['name' => 'Customer']);
        $customer->givePermissionTo('ver carga', 'crear carga', 'editar carga', 'eliminar carga');

        $traffic = Role::create(['name' => 'Traffic']);
        $traffic->givePermissionTo([
            'ver carga'
        ]);

        $transport = Role::create(['name' => 'Transport']);
        $transport->givePermissionTo('ver carga');

        // Asignar rol a un usuario específico (ejemplo)
        $usuario = User::find(77); // Cambia el ID por el usuario adecuado
        $usuario->assignRole('Master');
    }
}
