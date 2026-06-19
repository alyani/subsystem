<?php

namespace Alyani\Subsystem\Database\Seeders;

use Alyani\Subsystem\Enums\ActivationStatus;
use Alyani\Subsystem\Enums\Currency;
use Alyani\Subsystem\Enums\WithdrawalGatewayType;
use Alyani\Subsystem\Models\WithdrawalGateway;
use Illuminate\Database\Seeder;

class WithdrawalGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WithdrawalGateway::firstOrCreate([
            'name' => 'manual',
        ], [
            'title' => [
                'fa' => 'توسط مدیر',
                'en' => 'By admin',
            ],
            'type' => WithdrawalGatewayType::Manual,
            'currency' => Currency::IRR,
            'transaction_fee_percentage' => 0,
            'status' => ActivationStatus::Active,
        ]);
    }
}
