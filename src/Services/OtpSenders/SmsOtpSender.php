<?php

namespace Alyani\Subsystem\Services\OtpSenders;

use Alyani\Subsystem\Contracts\OtpSenderInterface;
use Alyani\Subsystem\Facade\SmsService;
use Illuminate\Support\Facades\Config;

class SmsOtpSender implements OtpSenderInterface
{
    public function send(array &$otpData): void
    {
        // Sender is allowed to mutate: method
        $otpData['method'] = $this->getMethod($otpData);

        SmsService::set('mobile', $otpData['authorityValue'])
            ->set('method', $otpData['method'])
            ->set('code', $otpData['code'])
            // templateID may be null depending on driver
            ->set('templateID', $this->getTemplateID($otpData))
            ->sendOTP();
    }

    public function supportsVerify(): bool
    {
        return false;
        // @todo handle verify for msgway (sms & ivr)
        // return SmsService::supportsVerifyOTP();
    }

    public function verify(array $otpData, $OTP): void
    {
        // @todo handle exception for msgway (sms & ivr)
        SmsService::set('mobile', $otpData['authorityValue'])
            ->set('OTP', $OTP)
            ->verifyOTP();
    }

    protected function getTemplateID($otpData)
    {
        if ($otpData['method'] === 'ivr') {
            return SmsService::getConfig('templates.OTP.IVR');
        } else {
            $locale = Config::get('app.locale');
            $defaultTemplate = SmsService::getConfig("templates.OTP.default.$locale", null);
            $actionTemplate = SmsService::getConfig("templates.OTP.{$otpData['action']}.$locale", null);
            return $actionTemplate ?? $defaultTemplate;
        }
    }

    protected function getMethod(array $otpData): string
    {
        $methods = SmsService::getConfig('OTPMethods', []);

        if (empty($methods)) {
            return 'sms';
        }

        if (empty($otpData['method'])) {
            return $methods[0];
        }

        $index = array_search($otpData['method'], $methods, true);

        if ($index === false) {
            return $methods[0];
        }

        return $methods[($index + 1) % count($methods)];
    }
}
