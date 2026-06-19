<?php

namespace Alyani\Subsystem\Services\Sms;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FarazsmsService extends BaseSmsService
{
    public function __construct()
    {
        $this->config = Config::get('smsService.providers.farazsms');
        $this->setHeader('Authorization', $this->config['key']);
        $this->setHeader('Content-Type', 'application/json');
    }

    public function supportsVerifyOTP()
    {
        return false;
    }

    public function sendOTP()
    {
        $this->url = $this->config['sendOTPUrl'] ?? '';

        $this->data = [
            'sending_type' => 'pattern',
            'from_number' => $this->config['from'],
            'recipients' => [
                $this->data['mobile']
            ],
            'code' => $this->data['templateID'],
            'params' => [
                'code' => $this->data['code'],
            ],
        ];

        return $this->request();
    }

    public function request()
    {
        if (empty($this->headers['Authorization'])) {
            throw new Exception('API key is missing');
        }

        if (empty($this->url)) {
            throw new Exception('URL is missing');
        }

        $response = Http::withHeaders($this->headers)
            ->acceptJson()
            ->post($this->url, $this->data);

        $responseData = $response->json();
        if ($response->status() != 200 || !$responseData || empty($responseData['meta']['status'])) {
            Log::error('FarazsmsService: failed', [
                'url' => $this->url,
                'data' => $this->data,
                'httpCode' => $response->status(),
                'response' => $responseData,
            ]);
            throw new Exception('Request failed');
        }

        return $response;
    }
}
