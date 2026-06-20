<?php

namespace Alyani\Subsystem\Models;

use Alyani\Subsystem\Enums\Currency;
use Alyani\Subsystem\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;

class Transaction extends Model
{
    use HasFactory;

    protected $casts = [
        'base_amount' => 'integer',
        'VAT_amount' => 'integer',
        'amount' => 'integer',
        'currency' => Currency::class,
        'type' => TransactionType::class,
        'payment_date' => 'datetime',
    ];

    /**
     * Relation : user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic relation
     */
    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }
}
