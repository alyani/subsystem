<?php

return [
    'sqlDebug' => env('APP_SQL_DEBUG', false),
    'appName' => env('APP_NAME', 'Admin panel'),
    'appTitle' => env('APP_TITLE', 'Admin panel'),
    'smsService' => env('SMS_SERVICE', 'msgway'),
    'adminAuthModel' => \Alyani\Subsystem\Models\Manager::class,
    'singleToken' => false,
    'signupAuthorityKey' => 'email', // email | mobile
    'lastActivityUpdateInterval' => 0, // in Minutes, default is zero , user last activyt will be updated at each request
    'availableItemsPerPage' => [
        25,
        50,
        100,
        200,
    ],

    'storage' => [
        'extrenalServiceToken' => env('STORAGE_SERVICE_TOKEN'),
        'path' => "uploads/",
        'pathTemporaryUploads' => "uploads/tmp/",
        'heavyUploaderCustomDirectory' => "heavyUploader",
        'tinymceCustomDirectory' => "tinymce",
        'audio' => [
            'validate' => [
                'max:10240', //10MB
                'mimes:mp3',
            ],
        ],
        'image' => [
            'convertToWebp' => true,
            'originalConversionQuality' => 70,
            'thumbnailConversionQuality' => 70,
            'thumbnail' => [
                'width' => 300,
                'height' => 300,
                'pathThumbnail' => 'thumbnails/',
            ],
            'validate' => [
                'max:10240', //10MB
                'mimes:png,jpg,jpeg',
            ],
        ],
        'excel' => [
            'validate' => [
                'max:51200', //50MB
                'mimes:xls,xlsx',
            ],
        ],
        'pdf' => [
            'validate' => [
                'max:51200', //50MB
                'mimes:pdf',
            ],
        ],
        'video' => [
            'validate' => [
                'max:1048576', // 1GB
                'mimes:mp4,avi,mpeg,mov',
            ],
            'originalConversionQuality' => 70,
            'thumbnailConversionQuality' => 70,
            'thumbnail' => [
                'width' => 300,
                'height' => 300,
                'pathThumbnail' => 'thumbnails/',
                'ext' => 'jpg',
            ],
        ],
    ],

    'defaultRoles' => [
        'user' => [
            'name' => 'کاربر عادی',
            'description' => 'Default role'
        ],
        'author' => [
            'name' => 'نویسنده',
            'description' => 'Default role'
        ],
    ],

    'otpService' => [
        'sandbox' => [ // if true, static code will be send
            'enable' => true,
            'code' => 12345
        ],
        'maxSendRetry' => 3,
        'maxVerifyRetry' => 3,
        'cacheExpiry' => 300,
    ],

    'finance' => [
        'debug' => false, // log trace of payment
        'sandbox' => false, // enable sandbox for payment verfication
        'currencies' => ['IRR'],
        'currenciesDisplay' => ['IRR' => 'IRT'],
        'ipgCache' => [
            'prefix' => 'ipg_',
            'expiry' => 600, // 10 minutues
        ],
        'paymentExpiresInMinutes' => 30, // minutes
        'gatewayZarinpal' => [
            // sandbox
            'startupURL' => 'https://sandbox.zarinpal.com/pg/StartPay/',
            'requestURL' => 'https://sandbox.zarinpal.com/pg/v4/payment/request.json',
            'verifyURL' => 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json',
            'merchantID' => 'f2614aa3-3a49-4ffe-a1d9-753d5a38c33e',

            // production
            // 'startupURL' => 'https://www.zarinpal.com/pg/StartPay/',
            // 'requestURL' => 'https://api.zarinpal.com/pg/v4/payment/request.json',
            // 'verifyURL' => 'https://api.zarinpal.com/pg/v4/payment/verify.json',
            // 'merchantID' => '',
        ],
    ],
];
