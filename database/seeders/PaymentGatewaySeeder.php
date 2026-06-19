<?php

namespace Alyani\Subsystem\Database\Seeders;

use Alyani\Subsystem\Enums\ActivationStatus;
use Alyani\Subsystem\Enums\Currency;
use Alyani\Subsystem\Enums\PaymentGatewayType;
use Alyani\Subsystem\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentGateway::firstOrCreate(
            [
                'name' => 'manual',
            ],
            [
                'title' => [
                    'fa' => 'توسط مدیر',
                    'en' => 'By admin',
                ],
                'type' => PaymentGatewayType::Manual,
                'currency' => Currency::IRR,
                'transaction_fee_percentage' => 0,
                'status' => ActivationStatus::Active,
            ]
        );

        PaymentGateway::firstOrCreate(
            [
                'name' => 'zarinpal',
            ],
            [
                'title' => [
                    'fa' => 'زرین‌پال',
                    'en' => 'ZarinPal',
                ],
                'type' => PaymentGatewayType::Online,
                'currency' => Currency::IRR,
                'transaction_fee_percentage' => 0,
                'min_amount' => 10000,
                'status' => ActivationStatus::Inactive,
            ]
        );
    }
}
