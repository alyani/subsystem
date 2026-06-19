<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'fa' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ st('Verification Code') }}</title>
    <style>
        body { font-family: Tahoma, Arial, sans-serif; background-color: #f4f4f4; padding: 20px; margin: 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .code { font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #2d3748; margin: 20px 0; background: #edf2f7; padding: 15px; border-radius: 6px; display: inline-block; }
        .footer { margin-top: 30px; font-size: 12px; color: #718096; }
    </style>
</head>
<body>
    <div class="container">
        <h2>{{ st('Verification Code') }}</h2>
        <p>{{ st('Please use the code below to verify your account:') }}</p>
        
        <div class="code">
            {{ $otpCode }}
        </div>
        
        <p>{{ st('This code will expire in :minutes minutes.', ['minutes' => 2]) }}</p>
        <p style="color: #e53e3e; font-size: 14px;">{{ st('Do not share this code with anyone.') }}</p>
        
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. {{ st('All rights reserved.') }}
        </div>
    </div>
</body>
</html>