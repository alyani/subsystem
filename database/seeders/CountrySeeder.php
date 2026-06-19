<?php

namespace Alyani\Subsystem\Database\Seeders;

use Illuminate\Database\Seeder;
use Alyani\Subsystem\Models\Country;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $table = app(Country::class)->getTable();
        foreach (static::getSeeds() as $seed) {
            $seed['title_localized'] = json_encode($seed['title_localized']);
            DB::table($table)->insertOrIgnore($seed);
        }
    }

    /**
     * Return an array containing all the commonly
     * used data across the entire system.
     *
     * @return array
     */
    public static function getSeeds(): array
    {
        return [
            [
                'id' => '1',
                'title_localized' => [
                    'fa' => 'ایران',
                    'en' => 'Iran'
                ]
            ]
        ];
    }
}
