<?php

namespace Alyani\Subsystem\Database\Seeders;

use Alyani\Subsystem\Models\Manager;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('subsystemPermissions') as $group => $permissions) {
            foreach (array_keys($permissions) as $permission) {
                Permission::findOrCreate($permission, 'web');
            }
        }

        $superAdminRole = Role::findOrCreate('Super Admin', 'web');

        $firstManager = Manager::first();
        if ($firstManager) {
            $firstManager->assignRole($superAdminRole);
        }
    }
}
