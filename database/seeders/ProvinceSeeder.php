<?php

namespace Alyani\Subsystem\Database\Seeders;

use Illuminate\Database\Seeder;
use Alyani\Subsystem\Models\Province;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $table = app(Province::class)->getTable();
        foreach (static::getSeeds() as $country_id => $seeds) {
            foreach ($seeds as $seed) {
                $seed['country_id'] = $country_id;
                $seed['title_localized'] = json_encode($seed['title_localized']);
                DB::table($table)->insertOrIgnore($seed);
            }
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
            1 =>
                [
                    [
                        'id' => '13',
                        'title_localized' => [
                            'fa' => 'گیلان',
                            'en' => 'Gilan'
                        ]
                    ],
                    [
                        'id' => '15',
                        'title_localized' => [
                            'fa' => 'مازندران',
                            'en' => 'Mazandaran'
                        ]
                    ],
                    [
                        'id' => '17',
                        'title_localized' => [
                            'fa' => 'گلستان',
                            'en' => 'Golestan'
                        ]
                    ],
                    [
                        'id' => '21',
                        'title_localized' => [
                            'fa' => 'تهران',
                            'en' => 'Tehran'
                        ]
                    ],
                    [
                        'id' => '23',
                        'title_localized' => [
                            'fa' => 'سمنان',
                            'en' => 'Semnan'
                        ]
                    ],
                    [
                        'id' => '24',
                        'title_localized' => [
                            'fa' => 'زنجان',
                            'en' => 'Zanjan'
                        ]
                    ],
                    [
                        'id' => '25',
                        'title_localized' => [
                            'fa' => 'قم',
                            'en' => 'Qom'
                        ]
                    ],
                    [
                        'id' => '26',
                        'title_localized' => [
                            'fa' => 'البرز',
                            'en' => 'Alborz'
                        ]
                    ],
                    [
                        'id' => '28',
                        'title_localized' => [
                            'fa' => 'قزوین',
                            'en' => 'Qazvin'
                        ]
                    ],
                    [
                        'id' => '31',
                        'title_localized' => [
                            'fa' => 'اصفهان',
                            'en' => 'Isfahan'
                        ]
                    ],
                    [
                        'id' => '34',
                        'title_localized' => [
                            'fa' => 'کرمان',
                            'en' => 'Kerman'
                        ]
                    ],
                    [
                        'id' => '35',
                        'title_localized' => [
                            'fa' => 'یزد',
                            'en' => 'Yazd'
                        ]
                    ],
                    [
                        'id' => '38',
                        'title_localized' => [
                            'fa' => 'چهارمحال و بختیاری',
                            'en' => 'Chaharmahal and Bakhtiari'
                        ]
                    ],
                    [
                        'id' => '41',
                        'title_localized' => [
                            'fa' => 'آذربایجان شرقی',
                            'en' => 'East Azerbaijan'
                        ]
                    ],
                    [
                        'id' => '44',
                        'title_localized' => [
                            'fa' => 'آذربایجان غربی',
                            'en' => 'West Azerbaijan'
                        ]
                    ],
                    [
                        'id' => '45',
                        'title_localized' => [
                            'fa' => 'اردبیل',
                            'en' => 'Ardabil'
                        ]
                    ],
                    [
                        'id' => '51',
                        'title_localized' => [
                            'fa' => 'خراسان رضوی',
                            'en' => 'Razavi Khorasan'
                        ]
                    ],
                    [
                        'id' => '54',
                        'title_localized' => [
                            'fa' => 'سیستان و بلوچستان',
                            'en' => 'Sistan and Baluchestan'
                        ]
                    ],
                    [
                        'id' => '56',
                        'title_localized' => [
                            'fa' => 'خراسان جنوبی',
                            'en' => 'South Khorasan'
                        ]
                    ],
                    [
                        'id' => '58',
                        'title_localized' => [
                            'fa' => 'خراسان شمالی',
                            'en' => 'North Khorasan'
                        ]
                    ],
                    [
                        'id' => '61',
                        'title_localized' => [
                            'fa' => 'خوزستان',
                            'en' => 'Khuzestan'
                        ]
                    ],
                    [
                        'id' => '66',
                        'title_localized' => [
                            'fa' => 'لرستان',
                            'en' => 'Lorestan'
                        ]
                    ],
                    [
                        'id' => '71',
                        'title_localized' => [
                            'fa' => 'فارس',
                            'en' => 'Fars'
                        ]
                    ],
                    [
                        'id' => '74',
                        'title_localized' => [
                            'fa' => 'کهکیلویه و بویراحمد',
                            'en' => 'Kohgiluyeh and Boyer-Ahmad'
                        ]
                    ],
                    [
                        'id' => '76',
                        'title_localized' => [
                            'fa' => 'هرمزگان',
                            'en' => 'Hormozgan'
                        ]
                    ],
                    [
                        'id' => '77',
                        'title_localized' => [
                            'fa' => 'بوشهر',
                            'en' => 'Bushehr'
                        ]
                    ],
                    [
                        'id' => '81',
                        'title_localized' => [
                            'fa' => 'همدان',
                            'en' => 'Hamadan'
                        ]
                    ],
                    [
                        'id' => '83',
                        'title_localized' => [
                            'fa' => 'کرمانشاه',
                            'en' => 'Kermanshah'
                        ]
                    ],
                    [
                        'id' => '84',
                        'title_localized' => [
                            'fa' => 'ایلام',
                            'en' => 'Ilam'
                        ]
                    ],
                    [
                        'id' => '86',
                        'title_localized' => [
                            'fa' => 'مرکزی',
                            'en' => 'Markazi'
                        ]
                    ],
                    [
                        'id' => '87',
                        'title_localized' => [
                            'fa' => 'کردستان',
                            'en' => 'Kurdistan'
                        ]
                    ],
                    [
                        'id' => '88',
                        'title_localized' => [
                            'fa' => 'کشور',
                            'en' => 'Country'
                        ]
                    ]
                ]
        ];
    }
}
