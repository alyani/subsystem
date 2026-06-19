<?php

namespace Alyani\Subsystem\Database\Seeders;

use Illuminate\Database\Seeder;
use Alyani\Subsystem\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (config('subsystem.defaultRoles') as $role) {
            Role::updateOrCreate($role);
        }
    }
}
