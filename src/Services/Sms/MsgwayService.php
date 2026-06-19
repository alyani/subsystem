<?php

namespace Alyani\Subsystem\Services\Sms;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Alyani\Subsystem\Services\Sms\BaseSmsService;
use Exception;

class MsgwayService extends BaseSmsService
{
    public function __construct()
    {
        $this->config = Config::get('smsService.providers.msgway');
        $this->setHeader('apiKey', $this->config['key']);
    }

    public function supportsVerifyOTP()
    {
        // add support for verify otp for ivr and sms
        return false;
    }

    public function request()
    {
        if (empty($this->headers['apiKey'])) {
            throw new Exception('API key is missing');
        }

        if (empty($this->url)) {
            throw new Exception('URL is missing');
        }

        if (!empty($this->data['countryCode'])) {
            $this->data['countryCode'] = (int) $this->data['countryCode'];
        }

        $response = Http::withHeaders($this->headers)
            ->post($this->url, $this->data);
        $responseData = $response->json();
        if ($response->status() != 200 || !$responseData || $responseData['status'] != 'success') {
            Log::error('MsgWayService: failed', [
                'url' => $this->url,
                'data' => $this->data,
                'httpCode' => $response->status(),
                'response' => $response->json(),
            ]);
            throw new Exception('Request failed');
        }

        return $response;
    }
}
