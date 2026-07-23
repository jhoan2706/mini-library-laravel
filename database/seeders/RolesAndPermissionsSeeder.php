<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'books.create',
            'books.update',
            'books.delete',
            'books.view',
            'loans.checkout',
            'loans.checkin',
            'dashboard.view',
            'users.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Crear roles
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        $librarian = Role::firstOrCreate(['name' => 'librarian', 'guard_name' => 'web']);
        $librarian->syncPermissions([
            'books.create',
            'books.update',
            'books.delete',
            'books.view',
            'loans.checkout',
            'loans.checkin',
            'dashboard.view',
        ]);

        $member = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        $member->syncPermissions([
            'books.view',
            'loans.checkout',
        ]);
    }
}