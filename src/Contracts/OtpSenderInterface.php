<?php

namespace Alyani\Subsystem\Contracts;

interface OtpSenderInterface
{
    public function send(array &$otpData): void;

    public function verify(array $otpData, $OTP): void;

    public function supportsVerify(): bool;
}
