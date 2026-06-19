<?php

namespace Alyani\Subsystem\Services\OtpSenders;

use Alyani\Subsystem\Contracts\OtpSenderInterface;
use Alyani\Subsystem\Mail\SendOTPCodeMail;
use Illuminate\Support\Facades\Mail;

class EmailOtpSender implements OtpSenderInterface
{
    public function send(array &$otpData): void
    {
        // Sender is allowed to mutate: method
        $otpData['method'] = 'email';

        Mail::to($otpData['authorityValue'])
            ->send(new SendOTPCodeMail($otpData['code']));
    }

    public function supportsVerify(): bool
    {
        return false;
    }

    public function verify(array $otpData, $OTP): void
    {
        return;
    }
}
