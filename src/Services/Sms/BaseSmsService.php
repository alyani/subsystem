<?php

namespace Alyani\Subsystem\Services\Sms;

abstract class BaseSmsService
{
    protected array $config = [];
    protected array $headers = [];
    protected array $data = [];
    protected string $url = '';

    abstract public function request();

    public function setHeader($key, $value = ''): static
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function set($key, $value = ''): static
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function sendOTP()
    {
        $this->url = $this->config['sendOTPUrl'] ?? '';
        return $this->request();
    }

    public function verifyOTP()
    {
        $this->url = $this->config['verifyOTPUrl'] ?? '';
        return $this->request();
    }

    public function supportsVerifyOTP()
    {
        return false;
    }
}
