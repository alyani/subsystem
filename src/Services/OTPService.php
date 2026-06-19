<?php

namespace Alyani\Subsystem\Services;

use Alyani\Subsystem\Contracts\OtpSenderInterface;
use Exception;
use Illuminate\Support\Facades\Cache;

class OTPService
{
    public const SEND_RETRY_EXCEEDED = 1;
    public const SEND_VERIFY_EXCEEDED = 2;
    public const EXPIRED = 3;
    public const INVALID_OTP = 4;

    protected array $data = [];
    protected array $config;

    public function __construct(
        protected string $action,
        protected string $authorityKey,
        protected string $authorityValue,
        protected OtpSenderInterface $sender,
        protected array $extraData = [],
    ) {
        $this->config = array_merge(
            [
                'sandbox' => [ // if true, static code will be send
                    'enable' => false,
                    'code' => ''
                ],
                'maxSendRetry' => 3,
                'maxVerifyRetry' => 3,
                'cacheExpiry' => 300,
                
            ],
            config('subsystem.otpService', [])
        );
    }

    public function send(): void
    {
        $this->load(initCacheData: true);
        
        if ($this->data && $this->isVerifyRetryExceeded()) {
            throw new Exception(
                st('Verification attempts exceeded. Please wait before requesting a new code.'),
                self::SEND_VERIFY_EXCEEDED
            );
        }

        if ($this->isSendRetryExceeded()) {
            throw new Exception(
                st('Too many requests. Please wait :remained minutes before requesting a new code.', ['remained' => $this->getRemainingTime()]),
                self::SEND_RETRY_EXCEEDED
            );
        }

        $this->data['sendRetry']++;
        if (!$this->isSandbox()) {
            $this->sender->send($this->data);
        } else {
            $this->data['method'] = 'sandbox';
        }

        Cache::put(
            $this->cacheKey(),
            $this->data,
            max(1, $this->data['expireTime'] - (time() - $this->data['startTime']))
        );
    }

    public function verify($OTP): void
    {
        $this->load(initCacheData: false);
        if (!$this->data) {
            throw new Exception(
                st('The verification code has expired or is no longer valid. Please request a new code.'),
                self::EXPIRED
            );
        }

        if ($this->isVerifyRetryExceeded()) {
            throw new Exception(
                st('Too many incorrect attempts. Please wait :remained minutes before trying again.', ['remained' => $this->getRemainingTime()]),
                self::SEND_VERIFY_EXCEEDED
            );
        }

        // verify OTP
        if (
            !$this->isSandbox() &&
            $this->sender->supportsVerify() &&
            $this->sender->verify($this->data, $OTP)
        ) {
            Cache::forget($this->cacheKey());
            return;
        } elseif (
            !empty($this->data['code']) &&
            $this->data['code'] === (string) $OTP
        ) {
            Cache::forget($this->cacheKey());
            return;
        }

        $this->data['verifyRetry']++;
        Cache::put(
            $this->cacheKey(),
            $this->data,
            max(1, $this->data['expireTime'] - (time() - $this->data['startTime']))
        );
        throw new Exception(
            st('The verification code is incorrect.'),
            self::INVALID_OTP
        );
    }

    public function data($key = null)
    {
        return $key ? ($this->data[$key] ?? null) : $this->data;
    }

    public function getSentPublicMessage(): string
    {
        return st(
            'The verification code has been sent to :target via :method.',
            [
                'target' => $this->authorityValue,
                'method' => st("sendOTPMethod.{$this->data['method']}"),
            ]
        );
    }

    protected function load($initCacheData = false): void
    {
        $cached = Cache::get($this->cacheKey());
        if ($cached && (time() - $cached['startTime']) < $cached['expireTime']) {
            $this->data = $cached;
            return;
        }

        Cache::forget($this->cacheKey());

        if ($initCacheData) {
            $this->data = [
                'action' => $this->action,
                'authorityKey' => $this->authorityKey,
                'authorityValue' => $this->authorityValue,
                'code' => (string) $this->generateCode(),
                'sendRetry' => 0,
                'verifyRetry' => 0,
                'startTime' => time(),
                'expireTime' => $this->config['cacheExpiry'],
                'extraData' => $this->extraData,
            ];
        }
    }

    protected function isSendRetryExceeded(): bool
    {
        return $this->data['sendRetry'] >= $this->config['maxSendRetry'];
    }

    protected function isVerifyRetryExceeded(): bool
    {
        return $this->data['verifyRetry'] >= $this->config['maxVerifyRetry'];
    }

    protected function getRemainingTime(): int
    {
        return ceil(($this->data['expireTime'] - (time() - $this->data['startTime'])) / 60);
    }

    protected function isSandbox(): bool
    {
        return $this->config['sandbox']['enable'] ?? false;
    }

    protected function cacheKey(): string
    {
        if ($this->authorityKey === 'mobile') {
            $authorityValue = preg_replace('/\D/', '', $this->authorityValue);
        } else {
            $authorityValue = strtolower($this->authorityValue);
        }
        return "otp-{$this->action}-{$this->authorityKey}-{$authorityValue}";
    }

    protected function generateCode(): string
    {
        if ($this->isSandbox()) {
            return (string) ($this->config['sandbox']['code'] ?? 12345);
        }

        return (string) rand(10000, 99999);
    }
}
