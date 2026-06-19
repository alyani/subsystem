<?php

return [
    'default' => env('SMS_SERVICE', 'farazsms'),

    'providers' => [
        'msgway' => [
            'key' => env('MSGWAY_API_KEY'),
            'sendOTPUrl' => 'https://api.msgway.com/otp/send',
            'verifyOTPUrl' => 'https://api.msgway.com/otp/verify',
            'OTPMethods' => ['sms', 'ivr'],
            'templates' => [
                'OTP' => [
                    'IVR' => 2,
                    'default' => [
                        'fa' => 3,
                        'en' => 3,
                    ],
                    'register' => [
                        'fa' => 3,
                        'en' => 3,
                    ],
                    'setMobile' => [
                        'fa' => 3,
                        'en' => 3,
                    ],
                    'resetPassword' => [
                        'fa' => 3,
                        'en' => 3,
                    ],
                    'changeMobileValidateCurrent' => [
                        'fa' => 3,
                        'en' => 3,
                    ],
                    'changeMobileValidateNew' => [
                        'fa' => 3,
                        'en' => 3,
                    ],
                    'changeEmailValidateCurrent' => [
                        'fa' => 3,
                        'en' => 3,
                    ],
                    'changeEmailValidateNew' => [
                        'fa' => 3,
                        'en' => 3,
                    ],
                ],
            ],
        ],

        'farazsms' => [
            'key' => env('FARAZSMS_API_KEY'),
            'from' => env('FARAZSMS_FROM'),
            'sendOTPUrl' => 'https://edge.ippanel.com/v1/api/send',
            'OTPMethods' => ['sms'],
            'templates' => [
                'OTP' => [
                    'default' => [
                        'fa' => 3,
                        'en' => 3,
                    ],
                ]
            ],
        ],
    ],
];
