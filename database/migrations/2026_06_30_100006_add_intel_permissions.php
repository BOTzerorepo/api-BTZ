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
            'ver_diagnosticos',
            'gestionar_diagnosticos',
            'ver_insights',
            'gestionar_insights',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'api']);
        }

        $comercial = Role::firstOrCreate(['name' => 'Comercial', 'guard_name' => 'api']);
        $comercial->givePermissionTo($permissions);

        $adminComercial = Role::firstOrCreate(['name' => 'AdminComercial', 'guard_name' => 'api']);
        $adminComercial->givePermissionTo(Permission::where('guard_name', 'api')->get());
    }

    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::whereIn('name', [
            'ver_diagnosticos',
            'gestionar_diagnosticos',
            'ver_insights',
            'gestionar_insights',
        ])->delete();
    }
};
