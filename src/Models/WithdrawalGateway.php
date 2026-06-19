<?php

namespace Alyani\Subsystem\Models;

use Alyani\Subsystem\Casts\AsArray;
use Alyani\Subsystem\Enums\ActivationStatus;
use Alyani\Subsystem\Enums\Currency;
use Alyani\Subsystem\Enums\WithdrawalGatewayType;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WithdrawalGateway extends Model
{
    use HasFactory;

    protected $casts = [
        'title' => AsArray::class,
        'type' => WithdrawalGatewayType::class,
        'currency' => Currency::class,
        'transaction_fee_percentage' => 'integer',
        'min_amount' => 'integer',
        'max_amount' => 'integer',
        'status' => ActivationStatus::class,
    ];
}
