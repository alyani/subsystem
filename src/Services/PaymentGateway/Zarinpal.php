<?php

namespace Alyani\Subsystem\Services\PaymentGateway;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class Zarinpal extends BaseGatewayService
{
    protected $authority = '';
    protected $verifyGatewayData = [];
    protected $verifyCode = '';
    protected $verifyError = '';
    protected $validVerifyCodes = [
        100,
        101,
    ];

    public const AUTHORIZE_INVALID_PAYMENT_ID = '01';
    public const AUTHORIZE_INVALID_AMOUNT = '02';
    public const AUTHORIZE_INVALID_CURRENCY = '03';
    public const AUTHORIZE_INVALID_CALLBACK_URL = '04';
    public const AUTHORIZE_HTTP_FAILURE = '05';
    public const AUTHORIZE_FAILED = '06';
    public const NOT_AUTHORIZED = '07';
    public const VERIFY_INVALID_SALE_REFERENCE_ID = '08';
    public const VERIFY_INVALID_PAYMENT_ID = '09';
    public const VERIFY_GATEWAY_ERROR = '10';
    public const VERIFY_HTTP_FAULT = '11';
    public const VERIFY_FAILED = '12';
    public const PAYMENT_AUTHORITY_FAULT = '15';
    public const PAYMENT_AUTHORITY_MISMATCH = '16';

    public function __construct()
    {
        $this->_config = [
            'startupURL' => config('subsystem.finance.gatewayZarinpal.startupURL'),
            'requestURL' => config('subsystem.finance.gatewayZarinpal.requestURL'),
            'verifyURL' => config('subsystem.finance.gatewayZarinpal.verifyURL'),
            'merchantID' => config('subsystem.finance.gatewayZarinpal.merchantID'),
        ];
    }

    public function authorize()
    {
        if (!$this->paymentID) {
            throw new Exception('Invalid paymentID', static::AUTHORIZE_INVALID_PAYMENT_ID);
        }
        if (!$this->amount) {
            throw new Exception('Invalid amount', static::AUTHORIZE_INVALID_AMOUNT);
        }
        if ($this->currency != 'IRR') {
            throw new Exception('Invalid currency', static::AUTHORIZE_INVALID_CURRENCY);
        }
        if (!$this->callbackURL) {
            throw new Exception('Invalid callbackURL', static::AUTHORIZE_INVALID_CALLBACK_URL);
        }
        if ($this->isSandbox()) {
            $this->setAuthorizeToken('sandbox' . time());
            static::traceLog('Authorize request done [sandbox]', [
                'method' => class_basename(self::class) . '::' . __FUNCTION__ . ' (L:' . __LINE__ . ')'
            ]);
            return $this;
        }

        // Authorization
        try {
            $response = Http::timeout(30)->post($this->_config['requestURL'], [
                'merchant_id' => $this->_config['merchantID'],
                'amount' => $this->amount,
                'callback_url' => $this->callbackURL,
                'description' => $this->orderDescription,
                'metadata' => [
                    'mobile' => $this->mobile
                ],
            ]);

            if ($response->failed()) {
                throw new Exception('Failed status', static::AUTHORIZE_HTTP_FAILURE);
            }

            $result = $response->json();
        } catch (Throwable $e) {
            Log::error('Authorize request [HTTP]', [
                'method' => class_basename(self::class) . '::' . __FUNCTION__ . ' (L:' . __LINE__ . ')',
                'requestURL' => $this->_config['requestURL'],
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                ...isset($response) ? [
                    'status' => $response->status(),
                    'data' => $response->json(),
                ] : [],
            ]);
            if ($response ?? null) {
                $result = $response->json();
                if (!empty($result['errors'])) {
                    $code = $result['errors']['code'] ?? '';
                    $message = $result['errors']['message'] ?? '';
                    throw new Exception("Authorization failed [gatewayError:{$code}:{$message}]", static::AUTHORIZE_FAILED);
                }
            }
            throw new Exception('Authorization failed', static::AUTHORIZE_FAILED);
        }

        static::traceLog('Authorize request response', [
            'method' => class_basename(self::class) . '::' . __FUNCTION__ . ' (L:' . __LINE__ . ')',
            'status' => $response->status(),
            'data' => $response->json(),
        ]);

        if (!empty($result['errors'])) {
            $code = $result['errors']['code'] ?? '';
            $message = $result['errors']['message'] ?? '';
            throw new Exception("Authorization failed [gatewayError:{$code}:{$message}]", static::AUTHORIZE_FAILED);
        }

        if (($result['data']['code'] ?? '') != 100 || empty($result['data']['authority'])) {
            $code = $result['data']['code'] ?? '';
            throw new Exception("Authorization failed [code:{$code}]", static::AUTHORIZE_FAILED);
        }

        $this->setAuthorizeToken($result['data']['authority']);

        return $this;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function prepareRedirectParams(): array
    {
        if (!$this->authorizeToken) {
            throw new Exception('notAuthorized', static::NOT_AUTHORIZED);
        }

        $isSandbox = $this->isSandbox();
        if ($isSandbox) {
            $redirectParams = [
                'url' => $this->callbackURL,
                'params' => [
                    'Authority' => $this->authorizeToken,
                    'Status' => 'OK',
                ],
                'method' => 'GET',
            ];
        } else {
            $redirectParams = [
                'url' => $this->_config['startupURL'] . $this->authorizeToken,
                'params' => [],
                'method' => 'GET',
            ];
        }
        static::traceLog('Prepare redirect params' . ($isSandbox ? ' [sandbox]' : ''), [
            'method' => class_basename(self::class) . '::' . __FUNCTION__ . ' (L:' . __LINE__ . ')',
            'checkoutURL' => $redirectParams['url'],
        ]);
        return $redirectParams;
    }

    public function checkPaymentForVerify($request, $payment)
    {
        $gatewayData = $payment->gateway_data;

        $status = $request->query('Status');
        $this->authority = $request->query('Authority');
        $this->amount = $payment->amount;

        if ($this->amount <= 0) {
            throw new Exception('Invalid amount', static::VERIFY_INVALID_SALE_REFERENCE_ID);
        }
        if ($status != 'OK') {
            throw new Exception('Transaction status is not completed', static::VERIFY_GATEWAY_ERROR);
        }
        if (!$this->authority) {
            throw new Exception('Invalid authority', static::PAYMENT_AUTHORITY_FAULT);
        }
        if (empty($gatewayData['authorizeToken']) || $gatewayData['authorizeToken'] != $this->authority) {
            throw new Exception('Authorize token mismatch', static::PAYMENT_AUTHORITY_MISMATCH);
        }
        return true;
    }

    public function verify()
    {
        if ($this->isSandbox()) {
            static::traceLog('Verify request done [sandbox]', [
                'method' => class_basename(self::class) . '::' . __FUNCTION__ . ' (L:' . __LINE__ . ')',
            ]);
            $this->setGatewayReference('sandbox_' . uniqid('zr_', true));
            return true;
        }

        try {
            $response = Http::timeout(30)->post($this->_config['verifyURL'], [
                'merchant_id' => $this->_config['merchantID'],
                'amount' => $this->amount,
                'authority' => $this->authority
            ]);

            if ($response->failed()) {
                throw new Exception('Verify request failed', static::VERIFY_HTTP_FAULT);
            }

            $result = $response->json();
        } catch (Throwable $e) {
            Log::error('Verify request [HTTP]', [
                'method' => class_basename(self::class) . '::' . __FUNCTION__ . ' (L:' . __LINE__ . ')',
                'error' => $e->getMessage(),
                'verifyURL' => $this->_config['verifyURL'],
                ...isset($response) ? [
                    'httpStatus' => $response->status(),
                    'responseData' => $response->json(),
                ] : [],
            ]);
            if ($response ?? null) {
                $result = $response->json();
                if (!empty($result['errors'])) {
                    $code = $result['errors']['code'] ?? '';
                    $message = $result['errors']['message'] ?? '';
                    $this->verifyError = $code . ':' . $message;
                    throw new Exception("Verification failed [gatewayError:{$code}:{$message}]", static::VERIFY_HTTP_FAULT);
                }
            }
            throw new Exception('Verification failed', static::VERIFY_HTTP_FAULT);
        }

        static::traceLog('Verify request [HTTP] response', [
            'method' => class_basename(self::class) . '::' . __FUNCTION__ . ' (L:' . __LINE__ . ')',
            'status' => $response->status(),
            'data' => $response->json(),
        ]);

        $this->verifyCode = $result['data']['code'] ?? '';

        if (!empty($result['errors'])) {
            $code = $result['errors']['code'] ?? '';
            $message = $result['errors']['message'] ?? '';
            $this->verifyError = $code . ':' . $message;
            throw new Exception("Verify failed [gatewayError:{$code}:{$message}]", static::VERIFY_FAILED);
        }

        if (
            !in_array((int) ($result['data']['code'] ?? ''), $this->validVerifyCodes) ||
            empty($result['data']['ref_id'])
        ) {
            $code = $result['data']['code'] ?? '';
            throw new Exception("Verify failed [code:{$code}]", static::VERIFY_FAILED);
        }

        $this->verifyGatewayData = $result['data'] ?? [];

        $this->setGatewayReference($result['data']['ref_id']);

        return true;
    }

    public function paymentGatewayData(): array
    {
        return array_filter([
            'verifyGatewayData' => $this->verifyGatewayData,
            'verifyCode' => $this->verifyCode,
            'verifyError' => $this->verifyError,
        ]);
    }
}
