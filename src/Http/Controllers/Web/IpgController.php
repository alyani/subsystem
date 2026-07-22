<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use Alyani\Subsystem\Enums\PaymentStatus;
use Alyani\Subsystem\Models\Payment;
use Alyani\Subsystem\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use InvalidArgumentException;

class IpgController extends Controller
{
    public function index(Request $request, string $token)
    {
        if ($request->isMethod('HEAD')) {
            return response('');
        }

        $data = Payment::getDataByIPGToken($token);
        if (!$data || empty($data['payment_id'])) {
            return $this->error(st('Request has been expired'));
        }
        $data += [
            'locale' => App::getLocale(),
            'theme' => 'light',
            'mobile' => '',
        ];
        $this->setIPGConfig($data['payment_id'], $data);

        // Add default params to log
        Log::withContext([
            'controllerMethod' => class_basename(self::class) . '::' . __FUNCTION__,
            'payment_id' => $data['payment_id'],
        ]);
        Payment::traceLog(
            'Start IPG landing',
            [
                'ip' => getClientIP(),
                'sandbox' => config('subsystem.finance.sandbox', false),
                'data' => $data
            ],
            ['backtrace' => true]
        );

        // Set locale
        app()->setLocale($data['locale']);

        // Get payment with row lock to prevent race conditions
        $payment = Payment::query()
            ->whereKey($data['payment_id'])
            ->where('status', PaymentStatus::Pending)
            ->lockForUpdate()
            ->first();
        if (!$payment) {
            Payment::traceLog('Payment not found or already processed');
            return $this->error(st('Payment not found'));
        }

        // Get gateway
        $paymentGateway = PaymentGateway::active()->online()->first();
        if (!$paymentGateway) {
            Payment::traceLog('Gateway not found');
            $payment->setFailed(['ipgLandingError' => 'noActiveGateway']);
            return $this->error(st('There is no active online gateways at the moment'), $payment);
        }

        // Set gateway transaction fee
        if ($paymentGateway->transaction_fee_percentage && !$payment->transaction_fee_amount) {
            $payment->transaction_fee_amount = ($payment->amount * $paymentGateway->transaction_fee_percentage) / 100;
            $payment->amount += $payment->transaction_fee_amount;
        }

        // Use atomic update with status check to prevent race conditions
        $affected = Payment::query()
            ->whereKey($payment->id)
            ->where('status', PaymentStatus::Pending)
            ->update([
                'ip' => getClientIP(),
                'payment_gateway_id' => $paymentGateway->id,
                'transaction_fee_amount' => $payment->transaction_fee_amount,
                'amount' => $payment->amount,
            ]);

        if ($affected !== 1) {
            Payment::traceLog('Payment already processed or invalid status (before authorize)');
            // Refresh payment status to return accurate error
            $payment->refresh();
            if ($payment->status !== PaymentStatus::Pending) {
                return $this->error(st('Payment already processed'));
            }
            return $this->error(st('Payment already processed or invalid status'));
        }

        // check minimum payment
        if ($paymentGateway->min_amount && $paymentGateway->min_amount > $payment->amount) {
            Payment::traceLog('Amount below minimum');
            $payment->setFailed(['ipgLandingError' => 'errorOnMinimumGateway']);
            $errorMessage = st('The minimum amount is :min_amount', [
                'min_amount' => formatAmount($paymentGateway->min_amount, $paymentGateway->currency->value)
            ]);
            return $this->error($errorMessage, $payment);
        }

        if (!is_null($payment->invoiceable_type)) {
            $orderDescription = $payment->invoiceable_type::getPayableTranslate();
        }

        // Gateway authorize
        try {
            // call gateway, to get auth token
            $gateway = $this->initializeGateway($paymentGateway->name)
                ->setAmount($payment->amount)
                ->setCurrency($payment->currency->value)
                ->setPaymentID($payment->id)
                ->setMobile($data['mobile'])
                ->setOrderDescription($orderDescription ?? 'شارژ کیف پول')
                ->setCallbackURL(route('ipgVerify', ['payment_uuid' => $payment->uuid]))
                ->authorize();
        } catch (Exception $e) {
            $payment->setFailed([
                'gatewayAuthorizeError' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                ]
            ]);
            $errorMessage = st('Payment connection failed') . " ({$e->getCode()})";
            return $this->error($errorMessage, $payment);
        }

        // Save authorizeToken with row lock to prevent race conditions
        $affected = Payment::query()
            ->whereKey($payment->id)
            ->where('status', PaymentStatus::Pending)
            ->update([
                'gateway_data' => ['authorizeToken' => $gateway->getAuthorizeToken()],
            ]);

        if ($affected !== 1) {
            Payment::traceLog('Payment already processed or invalid status (after authorize)');
            return $this->error(st('Payment already processed or invalid status'));
        }

        // Refresh payment to ensure consistency
        $payment->refresh();

        $gatewayRedirectParams = $gateway->prepareRedirectParams();
        Payment::traceLog('End IPG landing', compact('gatewayRedirectParams'));

        return view('subsystem::ipg.index', [
            'gateway' => $gatewayRedirectParams,
            'theme' => $data['theme']
        ]);
    }

    public function verify(Request $request, string $paymentUuid)
    {
        if ($request->isMethod('HEAD')) {
            return response('');
        }

        if (empty($paymentUuid)) {
            return $this->error(st('Error in processing'));
        }

        // Add default params to log
        Log::withContext([
            'controllerMethod' => class_basename(self::class) . '::' . __FUNCTION__,
        ]);
        Payment::traceLog('Start IPG verify', [
            'requestData' => $request->all(),
            'ip' => getClientIP(),
            'sandbox' => config('subsystem.finance.sandbox', false),
            'payment_uuid' => $paymentUuid,
        ]);

        // Verify pending payment
        $payment = Payment::where('uuid', $paymentUuid)->first();
        if (!$payment) {
            Payment::traceLog('Payment not found');
            return $this->error(st('Payment not found'));
        }

        // Add default params to log
        Log::withContext([
            'payment_id' => $payment->id,
        ]);

        if ($payment->status == PaymentStatus::Verified) {
            Payment::traceLog('Payment already verified');
            return $this->success(null, $payment);
        }
        if ($payment->status != PaymentStatus::Pending) {
            Payment::traceLog('Payment already processed', ['paymentStatus' => $payment->status->value]);
            return $this->error(st('Payment already processed or invalid status'), $payment);
        }

        // Verify gateway
        $paymentGateway = PaymentGateway::query()
            ->whereKey($payment->payment_gateway_id)
            ->active()
            ->online()
            ->first();
        if (!$paymentGateway) {
            Payment::traceLog('Invalid gateway name');
            return $this->error(null);
        }

        Payment::traceLog('Gateway selected: ' . $paymentGateway->name);

        // Check if the payment is expired
        if (now() >= $payment->created_at->addMinutes(config('subsystem.finance.paymentExpiresInMinutes', 30))) {
            Payment::traceLog('Payment expired');
            $payment->setFailed([
                'ipgVerifyError' => 'expired',
                'gatewayInput' => $request->all(),
            ]);
            return $this->error(null, $payment);
        }

        try {
            $gateway = $this->initializeGateway($paymentGateway->name);
            $gateway->checkPaymentForVerify($request, $payment);
        } catch (Exception $e) {
            Log::error('Invalid payment info', [
                'error' => $e->getMessage(),
            ]);
            return $this->error(null, $payment);
        }

        try {
            $gateway->verify();
        } catch (Exception $e) {
            $payment->setFailed([
                'gatewayVerifyError' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                ],
                $gateway->paymentGatewayData()
            ]);
            return $this->error(null, $payment);
        }

        if (!Payment::isGatewayReferenceUnique($gateway->getGatewayReference())) {
            $payment->setFailed([
                'ipgVerifyError' => 'duplicatedGatewayReference',
                'gatewayInput' => $request->all(),
            ]);
            return $this->error(null, $payment);
        }

        // verify payment
        try {
            $payment->setVerified(
                $gateway->getGatewayReference(),
                $gateway->paymentGatewayData(),
            );
        } catch (Exception $e) {
            Log::error('Invalid payment info', [
                'error' => $e->getMessage(),
            ]);
            return $this->error(null, $payment);
        }

        Payment::traceLog('End IPG verify');

        return $this->success(null, $payment);
    }

    public function error($message = '', $payment = null)
    {
        return $this->response('error', [
            'message' => $message,
            'payment' => $payment,
        ]);
    }

    public function success($message = '', $payment = null)
    {
        return $this->response('success', [
            'message' => $message,
            'payment' => $payment,
        ]);
    }

    public function response(string $status, $params = [])
    {
        $redirect = false;
        $payment = $params['payment'] ?? null;
        $message = $params['message'] ?? null;
        $returnUrl = null;
        $ipgConfig = [];

        if ($payment) {
            // include message in return url only if redirect is true
            $returnUrl = $payment->getReturnUrl(
                $status,
                $redirect ? $message : '',
                [
                    'invoice_id' => $payment->invoiceable_id ?? 0,
                ],
            );
            $ipgConfig = $this->getIPGConfig($payment->id);
        }

        // change locale
        if (!empty($ipgConfig['locale'])) {
            app()->setLocale($ipgConfig['locale']);
        }

        if (!empty($returnUrl) && $redirect) {
            return redirect()->away($returnUrl);
        }

        return view('subsystem::ipg.callback', array_filter([
            'status' => $status,
            'message' => $message,
            'returnUrl' => $returnUrl,
            'theme' => $ipgConfig['theme'] ?? 'light'
        ]));
    }

    protected function setIPGConfig(int $paymentId, $data = [])
    {
        Cache::set('finance.IPGConfig.payment-' . $paymentId, $data, config('subsystem.finance.ipgCache.expiry', 600));
    }

    protected function getIPGConfig(int $paymentId)
    {
        $key = 'finance.IPGConfig.payment-' . $paymentId;
        $config = Cache::get($key, []);
        Cache::forget($key);
        return $config;
    }

    private function initializeGateway(string $gatewayName, string $namespace = 'Alyani\Subsystem\Services\PaymentGateway\\')
    {
        $class = $namespace . ucfirst($gatewayName);

        if (!class_exists($class)) {
            throw new InvalidArgumentException("Gateway class '{$class}' not found.");
        }
        return new $class();
    }
}
