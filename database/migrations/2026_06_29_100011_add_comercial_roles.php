<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'ver_tarifario',
            'gestionar_tarifario',
            'ver_clientes_comercial',
            'gestionar_clientes_comercial',
            'ver_cotizaciones',
            'gestionar_cotizaciones',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'api']);
        }

        $comercial = Role::firstOrCreate(['name' => 'Comercial', 'guard_name' => 'api']);
        $comercial->givePermissionTo([
            'ver_tarifario',
            'gestionar_tarifario',
            'ver_clientes_comercial',
            'gestionar_clientes_comercial',
            'ver_cotizaciones',
            'gestionar_cotizaciones',
        ]);

        $adminComercial = Role::firstOrCreate(['name' => 'AdminComercial', 'guard_name' => 'api']);
        $adminComercial->givePermissionTo(Permission::where('guard_name', 'api')->get());
    }

    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::whereIn('name', ['Comercial', 'AdminComercial'])->delete();

        Permission::whereIn('name', [
            'ver_tarifario',
            'gestionar_tarifario',
            'ver_clientes_comercial',
            'gestionar_clientes_comercial',
            'ver_cotizaciones',
            'gestionar_cotizaciones',
        ])->delete();
    }
};
