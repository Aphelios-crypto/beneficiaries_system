<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        Permission::firstOrCreate(['name' => 'manage-users', 'guard_name' => 'web']);

        // Create roles for the web guard
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin      = Role::firstOrCreate(['name' => 'Admin',       'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Employee',    'guard_name' => 'web']);

        // Assign permissions to roles
        $superAdmin->givePermissionTo('manage-users');
        $admin->givePermissionTo('manage-users');

        $this->command->info('Roles created: Super Admin, Admin, Employee');
        $this->command->info('Permissions created: manage-users');
    }
}
