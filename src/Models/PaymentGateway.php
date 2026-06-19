<?php

namespace Alyani\Subsystem\Models;

use Alyani\Subsystem\Casts\AsArray;
use Alyani\Subsystem\Enums\ActivationStatus;
use Alyani\Subsystem\Enums\Currency;
use Alyani\Subsystem\Enums\PaymentGatewayType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $casts = [
        'title' => AsArray::class,
        'type' => PaymentGatewayType::class,
        'currency' => Currency::class,
        'transaction_fee_percentage' => 'integer',
        'min_amount' => 'integer',
        'max_amount' => 'integer',
        'status' => ActivationStatus::class,
    ];

    public function scopeActive(Builder $query)
    {
        return $query->where('status', ActivationStatus::Active);
    }

    public function scopeOnline(Builder $query)
    {
        return $query->where('type', PaymentGatewayType::Online);
    }

    public static function getForItemPicker()
    {
        return static::select('id', 'title', 'status')
            ->orderBy('id', 'asc')
            ->get()
            ->mapWithKeys(function ($item) {
                $title = $item->title[config('app.locale')] ?? current($item->title);
                if ($item->status == ActivationStatus::Inactive) {
                    $title .= ' «' . st('Inactive') . '»';
                }
                return [$item->id => $title];
            })
            ->toArray();
    }
}
