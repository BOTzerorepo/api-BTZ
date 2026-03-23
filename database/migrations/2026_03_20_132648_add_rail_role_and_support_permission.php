<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'soporte', 'guard_name' => 'api']);

        $rail = Role::firstOrCreate(['name' => 'Rail', 'guard_name' => 'api']);
        $rail->givePermissionTo(Permission::where('guard_name', 'api')->get());
    }

    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::where('name', 'Rail')->delete();
        Permission::where('name', 'soporte')->delete();
    }
};
