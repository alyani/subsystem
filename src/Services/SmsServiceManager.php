<?php

namespace Alyani\Subsystem\Services;

use Exception;
use Illuminate\Support\Facades\Config;

class SmsServiceManager
{
    protected $serviceInstance;
    protected string $serviceName;

    /**
     * @throws \Exception
     */
    public function __construct($serviceName = null)
    {
        $this->setService($serviceName ?? Config::get('smsService.default'));
    }

    /**
     * @throws \Exception
     */
    public function setService($serviceName)
    {
        $class = __NAMESPACE__ . '\\Sms\\' . ucfirst($serviceName) . 'Service';

        if (!class_exists($class)) {
            throw new Exception("Service class {$class} not found");
        }

        $this->serviceInstance = new $class();
        $this->serviceName = $serviceName;
        return $this;
    }

    public function getConfig($key = null, $default = '')
    {
        $configKey = "smsService.providers.{$this->serviceName}";
        if ($key) {
            $configKey .= ".{$key}";
        }
        return Config::get($configKey, $default);
    }

    public function setHeader($key, $value = ''): static
    {
        $this->serviceInstance->setHeader($key, $value);
        return $this;
    }

    public function set($key, $value = ''): static
    {
        $this->serviceInstance->set($key, $value);
        return $this;
    }

    public function send()
    {
        return $this->serviceInstance->send();
    }

    public function sendOTP()
    {
        return $this->serviceInstance->sendOTP();
    }

    public function verifyOTP()
    {
        return $this->serviceInstance->verifyOTP();
    }

    public function supportsVerifyOTP()
    {
        return $this->serviceInstance->supportsVerifyOTP();
    }
}
