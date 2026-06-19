<?php

namespace Alyani\Subsystem\Models\Traits\Finance;

use Alyani\Subsystem\Enums\PaymentInvoiceStatus;
use Alyani\Subsystem\Enums\PaymentStatus;
use Alyani\Subsystem\Models\Payment;
use Alyani\Subsystem\Models\PaymentGateway;
use Alyani\Subsystem\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Use for invoiceable items
 * Add this trait to your model
 */
trait Invoiceable
{
    use Transactionable;

    /**
     * @var null|callable
     **/
    protected static $beforeInvoicePaid = null;

    /**
     * @var null|callable
     **/
    protected static $afterInvoicePaid = null;

    /**
     * Make new invoice
     */
    abstract public static function makeInvoice(array $params);

    public function getOnlinePaymentToken(array $params = [], $useBalance = false)
    {
        $paymentGateway = PaymentGateway::active()->online()->first();
        if (!$paymentGateway) {
            throw new Exception('There is no active online gateways at the moment');
        }
        $payableAmount = $this->getPayableAmount();
        if ($useBalance) {
            $payableAmount -= User::find($this->user_id)?->balance ?: 0;
        }
        if ($payableAmount <= 0) {
            throw new Exception('Payable amount is required');
        }
        $payment = new Payment();
        $payment->forceFill([
            'user_id' => $this->user_id,
            'payment_gateway_id' => $paymentGateway->id,
            'base_amount' => $this->getPayableBaseAmount(),
            'amount' => max($payableAmount, $paymentGateway->min_amount),
            'currency' => $this->getPayableCurrency(),
            'status' => PaymentStatus::Pending,
            'invoice_status' => PaymentInvoiceStatus::Pending,
        ]);
        $payment->invoiceable()->associate($this);
        $payment->save();
        return $payment->getIPGToken($params);
    }

     /**
     * Pay an invoice
     *
     * 1. Set invoice payment status to proccesing
     * 2. decrease user wallet (balance) + save transaction
     * 3. Set payment status to verified
     *
     * @param $params
     * @throws
     */
    public function pay(array $params = []): self
    {
        $this->debug('Invoice payment started', ['backtrace' => true]);

        // Execute the before payment closure if it exists
        if (is_callable(static::$beforeInvoicePaid)) {
            $this->debug('Call invoiceable event `beforeInvoicePaid`');
            call_user_func(static::$beforeInvoicePaid, $this, $params);
        }

        $this->changeInvoicePaymentStatus(
            fromStatus: PaymentStatus::Pending,
            toStatus: PaymentStatus::Processing
        );

        // Decrease wallet and save transaction
        try {
            User::decreaseBalance(
                userID: $this->user_id,
                transactionable: $this,
            );
        } catch (Throwable $e) {
            $error = 'Failed to decrease user balanace';
            Log::error($error, ['error' => $e->getMessage()]);
            $this->changeInvoicePaymentStatus(
                fromStatus: PaymentStatus::Processing,
                toStatus: PaymentStatus::Failed,
                extraParams: [
                    'error_data' => $error . ' :' . $e->getMessage(),
                ]
            );
            throw new Exception($error);
        }
        $this->debug('User balance is decreased');

        $this->changeInvoicePaymentStatus(
            fromStatus: PaymentStatus::Processing,
            toStatus: PaymentStatus::Verified,
            extraParams: [
                'paid_at' => now(),
            ]
        );

        // Execute the after payment closure if it exists
        if (is_callable(static::$afterInvoicePaid)) {
            $this->debug('Call invoiceable event `afterInvoicePaid`');
            call_user_func(static::$afterInvoicePaid, $this, $params);
        }

        return $this;
    }

    /**
     * Update invocie payment status
     */
    protected function changeInvoicePaymentStatus(
        PaymentStatus $fromStatus,
        PaymentStatus $toStatus,
        array $extraParams = []
    ) {
        try {
            $affected = static::where('id', $this->id)
                ->where('payment_status', $fromStatus)
                ->update(['payment_status' => $toStatus] + $extraParams);
            if (!$affected) {
                throw new Exception('No record is affected');
            }
        } catch (Throwable $e) {
            $error = "Failed to update invoice's payment status from `{$fromStatus->value}` to `{$toStatus->value}`";
            Log::error($error, [
                'error' => $e->getMessage(),
                'update_params' => $extraParams,
            ]);
            throw new Exception($error);
        }
        $this->debug("Invoice's payment status is updated from `{$fromStatus->value}` to `{$toStatus->value}`", array_filter([
            'update_params' => $extraParams,
        ]));
        return $this;
    }

    /**
     * Set a closure to run before an invoice is paid
     */
    protected static function beforeInvoicePaid(callable $callable)
    {
        static::$beforeInvoicePaid = $callable;
    }

    /**
     * Set a closure to run after an invoice is paid
     */
    protected static function afterInvoicePaid(callable $callable)
    {
        static::$afterInvoicePaid = $callable;
    }

    /**
     * Log payment for debugging
     */
    protected function debug($message, $params = [])
    {
        if (!config('subsystem.finance.debug')) {
            return;
        }
        $params = [
            'invoiceable' => static::class,
            'invoiceable_id' => $this->id,
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
}
