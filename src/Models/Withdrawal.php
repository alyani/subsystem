<?php

namespace Alyani\Subsystem\Models;

use Alyani\Subsystem\Casts\AsArray as CastsAsArray;
use Alyani\Subsystem\Contracts\Finance\TransactionableContract;
use Alyani\Subsystem\Enums\Currency;
use Alyani\Subsystem\Enums\WithdrawalStatus;
use Alyani\Subsystem\Models\Traits\Finance\Transactionable;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Throwable;
use Tsit\ApiSkeleton\Casts\AsArray;

class Withdrawal extends Model implements TransactionableContract
{
    use HasFactory;
    use Transactionable;

    protected $casts = [
        'base_amount' => 'integer',
        'transaction_fee_amount' => 'integer',
        'amount' => 'integer',
        'currency' => Currency::class,
        'status' => WithdrawalStatus::class,
        'gateway_data' => CastsAsArray::class,
        'extra_data' => AsArray::class,
    ];

    const ERROR_DECREASE_BALANCE = 1000;

    /**
     * Relation : user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation : manager
     */
    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }

    /**
     * Relation : withdrawalGateway
     */
    public function withdrawalGateway()
    {
        return $this->belongsTo(WithdrawalGateway::class);
    }

    /**
     * Set withdrawal status to verified
     * @param $gatewayData
     */
    public function setVerified()
    {
        // Decrease user balance
        try {
            User::decreaseBalance(
                userID: $this->user_id,
                transactionable: $this,
                transactionParams : ['description' => $this->gateway_data['description'] ?? '']
            );
        } catch (Throwable $e) {
            $error = 'Failed to decrease user balanace';
            Log::error($error, [
                'method' => 'Withdrawal::setVerified',
                'withdrawal_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            $this->status = WithdrawalStatus::Failed;
            $this->error_data = $error . ' : ' . $e->getMessage();
            $this->save();
            throw new Exception($error, self::ERROR_DECREASE_BALANCE);
        }

        $this->status = WithdrawalStatus::Verified;
        $this->save();

        return $this;
    }

    public static function getPayableTranslate(): string
    {
        return st('Money withdrawal');
    }

    public static function getPayableDetailAdminRoute(int $id = null)
    {
        return route('admin.withdrawal.list', ['id' => $id]);
    }
}
