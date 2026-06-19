<?php

namespace Database\Seeders;

use Alyani\Subsystem\Database\Seeders\CitySeeder;
use Alyani\Subsystem\Database\Seeders\CountrySeeder;
use Alyani\Subsystem\Database\Seeders\CreateManagerSeeder;
use Alyani\Subsystem\Database\Seeders\ProvinceSeeder;
use Alyani\Subsystem\Database\Seeders\UserSeeder;
use Illuminate\Database\Seeder;

class SubsystemSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CreateManagerSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(ProvinceSeeder::class);
        $this->call(CitySeeder::class);
    }
}
