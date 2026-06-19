<?php

namespace Alyani\Subsystem\Models\Traits\Finance;

use Alyani\Subsystem\Contracts\Finance\TransactionableContract;
use Alyani\Subsystem\Enums\TransactionType;
use Alyani\Subsystem\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

trait HasBalance
{
    /**
     * Increase user wallet and save transaction
     */
    public static function increaseBalance(
        int $userID,
        TransactionableContract $transactionable,
        array $transactionParams = []
    ): Transaction {
        DB::beginTransaction();

        try {
            $affected = static::query()
                ->where('id', $userID)
                ->where('currency', $transactionable->getPayableCurrency())
                ->update([
                    'balance' => DB::raw('balance + ' . $transactionable->getPayableAmount()),
                ]);

            if (!$affected) {
                throw new Exception('Balance update failed');
            }

            $transaction = new Transaction();
            $transaction->forceFill([
                'user_id'     => $userID,
                'base_amount' => $transactionable->getPayableBaseAmount(),
                'VAT_amount'  => $transactionable->getPayableVATAmount(),
                'amount'      => $transactionable->getPayableAmount(),
                'currency'    => $transactionable->getPayableCurrency(),
            ] + $transactionParams);

            $transaction->transactionable()->associate($transactionable);
            $transaction->type = TransactionType::Increase;
            $transaction->save();

            DB::commit();
            return $transaction;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Decrease user wallet and save transaction
     */
    public static function decreaseBalance(
        int $userID,
        TransactionableContract $transactionable,
        array $transactionParams = []
    ): Transaction {
        DB::beginTransaction();

        try {
            if ($transactionable->getPayableAmount() > 0) {
                $affected = static::query()
                    ->where('id', $userID)
                    ->where('currency', $transactionable->getPayableCurrency())
                    ->where('balance', '>=', $transactionable->getPayableAmount())
                    ->update([
                        'balance' => DB::raw('balance - ' . $transactionable->getPayableAmount()),
                    ]);
                if (!$affected) {
                    throw new Exception('Insufficient balance or update failed');
                }
            }

            $transaction = new Transaction();
            $transaction->forceFill([
                'user_id'     => $userID,
                'base_amount' => $transactionable->getPayableBaseAmount(),
                'VAT_amount'  => $transactionable->getPayableVATAmount(),
                'amount'      => $transactionable->getPayableAmount(),
                'currency'    => $transactionable->getPayableCurrency(),
            ] + $transactionParams);

            $transaction->transactionable()->associate($transactionable);
            $transaction->type = TransactionType::Decrease;
            $transaction->save();

            DB::commit();
            return $transaction;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
