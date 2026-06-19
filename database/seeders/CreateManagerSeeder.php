<?php

namespace Alyani\Subsystem\Database\Seeders;

use Alyani\Subsystem\Models\Manager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Manager::firstOrCreate([
            'mobile' => '+989360000000',
        ], [
            'name' => 'کاربر',
            'family' => 'مدیریت',
            'password' => Hash::make('1234'),
            'status' => 'active',
        ]);
    }
}
