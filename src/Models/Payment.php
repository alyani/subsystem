<?php

namespace Alyani\Subsystem\Models;

use Alyani\Subsystem\Casts\AsArray;
use Alyani\Subsystem\Contracts\Finance\TransactionableContract;
use Alyani\Subsystem\Enums\Currency;
use Alyani\Subsystem\Enums\PaymentInvoiceStatus;
use Alyani\Subsystem\Enums\PaymentStatus;
use Alyani\Subsystem\Models\Traits\Finance\Transactionable;
use Alyani\Subsystem\Models\Traits\Pagination;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class Payment extends Model implements TransactionableContract
{
    use HasFactory;
    use Transactionable;
    use Pagination;

    protected $casts = [
        'base_amount' => 'integer',
        'transaction_fee_amount' => 'integer',
        'amount' => 'integer',
        'currency' => Currency::class,
        'status' => PaymentStatus::class,
        'invoice_status' => PaymentInvoiceStatus::class,
        'gateway_data' => AsArray::class,
        'extra_data' => AsArray::class,
        'payment_date' => 'datetime',
    ];


    const ERROR_UPDATING_STATUS = 1000;
    const ERROR_INCREASE_BALANCE = 1001;
    const ERROR_UPDATING_INVOICE_STATUS = 1010;
    const ERROR_INVOICE_PAYMENT = 1011;

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
     * Relation : paymentGateway
     */
    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    /**
     * Polymorphic relation
     */
    public function invoiceable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Set payment status to failed
     *
     * @param $gatewayData
     * @param $paymentParamsToUpdate extra fields to be updated in payment DB
     * @return boolean
     */
    public function setFailed($gatewayData = [], $paymentParamsToUpdate = [])
    {
        Log::withContext([
            'method' => 'Payment::setFailed',
            'payment_id' => $this->id,
        ]);
        $this->debug('Start to set payment failure', ['backtrace' => true]);

        // Set gateway_data if exists
        $this->pushToGatewayData($gatewayData);
        if ($this->isDirty('gateway_data')) {
            $paymentParamsToUpdate['gateway_data'] = $this->gateway_data;
        }

        // Update payment & set status to failed
        $this->updatePaymentStatus(
            fromStatus: PaymentStatus::Pending,
            toStatus: PaymentStatus::Failed,
            extraParams: $paymentParamsToUpdate
        );

        $this->debug('End of setting payment failure');
        return true;
    }

    /**
     * 1. Set payment status to proccesing
     * 2. increase user wallet (balance) + save transaction
     * 3. Set payment status to verified
     * 4. if payment has invoiceable
     *      4.1. Set invoice status to proccesing
     *      4.2. pay invoice by calling invoiceable->pay()
     *          4.2.1. decrease user wallet (balance) + save transaction
     *          4.2.2. check for more ...
     *      4.3. Set invoice status status to completed
     *
     * @param $gatewayReference
     * @param $gatewayData
     * @param $paymentParamsToUpdate extra fields to be updated in payment DB
     * @throws
     */
    public function setVerified(
        ?string $gatewayReference = null,
        array $gatewayData = [],
        array $paymentParamsToUpdate = []
    ): self {
        Log::withContext([
            'method' => 'Payment::setVerified',
            'payment_id' => $this->id,
        ]);

        $this->debug('Payment verification started', ['backtrace' => true]);

        // Set gateway_data if exists
        $this->pushToGatewayData($gatewayData);
        if ($this->isDirty('gateway_data')) {
            $paymentParamsToUpdate['gateway_data'] = $this->gateway_data;
        }

        // Set gateway reference (must be unique)
        if ($gatewayReference) {
            $paymentParamsToUpdate['gateway_reference'] = $gatewayReference;
        }

        // Set payment date if not already set
        // (offline or manual gateways may set it earlier)
        if (empty($this->payment_date)) {
            $paymentParamsToUpdate['payment_date'] = now();
        }

        // Update payment & set status to processing
        $this->updatePaymentStatus(
            fromStatus: PaymentStatus::Pending,
            toStatus: PaymentStatus::Processing,
            extraParams: $paymentParamsToUpdate
        );

        try {
            // Increase user wallet balance
            User::increaseBalance(
                userID: $this->user_id,
                transactionable: $this,
                transactionParams : ['description' => $this->gateway_data['description'] ?? '']
            );
        } catch (Throwable $e) {
            $error = 'Failed to increase user balanace';
            // Set payment status to failed
            $this->updatePaymentStatus(
                fromStatus: PaymentStatus::Processing,
                toStatus: PaymentStatus::Failed,
                extraParams: [
                    'error_data' => $error . ' :' . $e->getMessage(),
                ]
            );
            Log::error($error, ['error' => $e->getMessage()]);
            throw new Exception($error, self::ERROR_INCREASE_BALANCE);
        }

        $this->debug('User balance is increased');

        // Set payment status to verified
        $this->updatePaymentStatus(
            fromStatus: PaymentStatus::Processing,
            toStatus: PaymentStatus::Verified,
        );

        $this->debug('Payment is verified');

        // Call invoiceable operation
        if ($this->invoiceable) {
            Log::withContext([
                'invoiceable' => $this->invoiceable->getMorphClass(),
                'invoiceable_id' => $this->invoiceable_id,
            ]);
            $this->debug('Has invoiceable operation');

            // Update invoice status
            $this->updateInvoiceStatus(
                fromStatus: PaymentInvoiceStatus::Pending,
                toStatus: PaymentInvoiceStatus::Processing,
            );

            // Pay invoice
            try {
                $this->invoiceable->pay();
            } catch (Exception $e) {
                $this->invoiceable->refresh();
                $error = 'Invoice payment had failed';
                // Update invoice status
                $this->updateInvoiceStatus(
                    fromStatus: PaymentInvoiceStatus::Processing,
                    toStatus: $this->invoiceable->payment_status == PaymentStatus::Verified
                        ? PaymentInvoiceStatus::PaidUncompleted
                        : PaymentInvoiceStatus::Failed,
                    extraParams: [
                        'error_data' => $error . ' :' . $e->getMessage(),
                    ]
                );
                Log::error($error, ['error' => $e->getMessage()]);
                throw new Exception($error, self::ERROR_INVOICE_PAYMENT);
            }

            $this->debug('Invoice payment is completed');

            // Set payment invoice status to completed
            $this->updateInvoiceStatus(
                fromStatus: PaymentInvoiceStatus::Processing,
                toStatus: PaymentInvoiceStatus::Completed,
            );
        }
        $this->debug('Payment verification ended');

        $this->refresh();
        return $this;
    }

    /**
     * Set data for ipg, and return a token
     */
    public function getIPGToken(array $params = []): string
    {
        $token = md5($this->id . uniqid());
        Cache::put(
            config('subsystem.finance.ipgCache.prefix', 'ipg_') . $token,
            [
                'payment_id' => $this->id,
                'locale' => config('app.locale')
            ] + $params,
            config('subsystem.finance.ipgCache.expiry', 600)
        );
        return $token;
    }

    public function pushToGatewayData(array $data)
    {
        $this->gateway_data = array_merge(($this->gateway_data ?: []), $data);
        return $this;
    }

    public function pushToExtraData(array $data)
    {
        $this->extra_data = array_merge(($this->extra_data ?: []), $data);
        return $this;
    }

    /**
     * Log payment for debugging
     */
    private function debug($message, $params = [])
    {
        if (!config('subsystem.finance.debug')) {
            return;
        }
        $params = [
            'payment_id' => $this->id,
        ] + $params;
        if ($params['backtrace'] ?? false) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
            $params['backtrace'] = [];
            foreach ($backtrace as $trace) {
                if (isset($trace['file']) && strpos($trace['file'], 'Illuminate')) {
                    break;
                }
                $params['backtrace'][] = $trace;
            }
        }
        Log::debug($message, $params);
    }

    /**
     * Update payment status
     */
    private function updatePaymentStatus(
        PaymentStatus $fromStatus,
        PaymentStatus $toStatus,
        array $extraParams = []
    ) {
        try {
            $affected = static::where('id', $this->id)
                ->where('status', $fromStatus)
                ->update(['status' => $toStatus] + $extraParams);
            if (!$affected) {
                throw new Exception('No record is affected');
            }
        } catch (Throwable $e) {
            $error = "Failed to update payment's status from `{$fromStatus->value}` to `{$toStatus->value}`";
            Log::error($error, [
                'error' => $e->getMessage(),
                'update_params' => $extraParams,
            ]);
            throw new Exception($error, self::ERROR_UPDATING_STATUS);
        }

        $this->debug("Payment's status is updated from `{$fromStatus->value}` to `{$toStatus->value}`", array_filter([
            'update_params' => $extraParams,
        ]));

        return $this;
    }

    /**
     * Update invoice status
     */
    private function updateInvoiceStatus(
        PaymentInvoiceStatus $fromStatus,
        PaymentInvoiceStatus $toStatus,
        array $extraParams = []
    ) {
        try {
            $affected = static::where('id', $this->id)
                ->where('invoice_status', $fromStatus)
                ->update(['invoice_status' => $toStatus] + $extraParams);
            if (!$affected) {
                throw new Exception('No record is affected');
            }
        } catch (Throwable $e) {
            $error = "Failed to update payment's invoice status from `{$fromStatus->value}` to `{$toStatus->value}`";
            Log::error($error, [
                'error' => $e->getMessage(),
                'update_params' => $extraParams,
            ]);
            throw new Exception($error, self::ERROR_UPDATING_INVOICE_STATUS);
        }

        $this->debug("Payment's invoice status is updated from `{$fromStatus->value}` to `{$toStatus->value}`", array_filter([
            'update_params' => $extraParams,
        ]));

        return $this;
    }

    /**
     * Check if given gateway reference, is unique
     */
    public static function isGatewayReferenceUnique(string $gatewayReference, array $status = []): bool
    {
        $payment = static::where('gatewayReference', $gatewayReference);
        if ($status) {
            $payment->whereIn('status', $status);
        }
        return $payment->count() ? false : true;
    }

    public static function getPayableTranslate(): string
    {
        return st('Add fund');
    }

    public static function getPayableDetailAdminRoute(int $id = null)
    {
        return route('admin.payment.list', ['id' => $id]);
    }
}
