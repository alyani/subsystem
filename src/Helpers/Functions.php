<?php


if (!function_exists('normalizeCountryCode')) {
    /**
     * Normalize country code
     *
     * @param string $country_code
     * @return string
     */
    function normalizeCountryCode($country_code): string
    {
        if (empty($country_code) || !is_numeric($country_code)) {
            return '';
        }
        $country_code = ltrim($country_code, '+');
        return replacePersianDigistWithEnglish($country_code);
    }
}

if (!function_exists('normalizeMobile')) {
    /**
     * Normalize a given mobile number by converting Persian digits to English and formatting it.
     *
     * @param string|integer $mobile The mobile number to normalize.
     * @return string The normalized mobile number, or an empty string if invalid.
     */
    function normalizeMobile($mobile, $countryCode = ''): string
    {
        $mobile = replacePersianDigistWithEnglish($mobile);
        $countryCode = replacePersianDigistWithEnglish($countryCode);

        if (!is_numeric($mobile)) {
            return '';
        }
        $mobile = trim($mobile);
        if (empty($mobile)) {
            return '';
        }
        if (!str_starts_with($mobile, '+') && !empty($countryCode) && is_numeric($countryCode)) {
            $mobile = (int)ltrim($mobile, 0);
            $mobile = $countryCode . $mobile;
        }

        switch (true) {
            case !$mobile || !preg_match('/^[\d\+]+$/', $mobile):
                break;
            case preg_match('/^(\+?98|00+98|0*)(9\d{9}+)$/', $mobile):
                $mobile = preg_replace('/^(\+?98|00+98|0*)(9\d+)$/', '+98$2', $mobile);
                break;
            case preg_match('/^[^0\+]/', $mobile):
                break;
            default:
                $mobile = preg_replace('/^(0+|\+)(\d+)$/', '+$2', $mobile);
        }
        return '+' . ltrim($mobile, '+');
    }
}

if (!function_exists('replacePersianDigistWithEnglish')) {
    /**
     * Replace Persian digits in a given string with their English counterparts.
     *
     * @param string $value The string containing Persian digits.
     * @return string The string with Persian digits replaced by English digits.
     */
    function replacePersianDigistWithEnglish($value): string
    {
        $persianDigits = ['۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '۰'];
        $englishDigits = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
        return str_replace($persianDigits, $englishDigits, $value);
    }
}

if (!function_exists('getMinute')) {
    /**
     * Get an array of minutes from 0 to 59.
     *
     * @return array The array of minutes.
     */
    function getMinute(): array
    {
        return range(0, 59);
    }
}

if (!function_exists('getHour')) {
    /**
     * Get an array of hours from 0 to 23.
     *
     * @return array The array of hours.
     */
    function getHour(): array
    {
        return range(0, 23);
    }
}

if (!function_exists('button')) {
    /**
     * Generate an HTML button element.
     *
     * @param string $name The name of the button.
     * @param string $route The route for the button link.
     * @param array $options Additional options for the button.
     * @return string The generated HTML button element.
     */
    function button(string $name, string $route, array $options = []): string
    {
        $defaults = $options + [
                'target' => '_blank',
                'title' => __('app.' . $name),
                'color' => 'primary',
                'class' => 'btn-icon btn btn-xs',
                'confirm' => '',
            ];
        $defaults['class'] .= " btn-{$defaults['color']}";
        if (!empty($defaults['confirm'])) {
            $defaults['confirm'] = 'onclick="return confirm(\'' . $defaults['confirm'] . '\')"';
        }
        $icon = view('subsystem::icons.' . $name)->render();

        return "<a href='{$route}'" . $defaults['confirm'] . "
                    target='{$defaults['target']}'
                    title='" . __($defaults['title']) . "'
                    class='{$defaults['class']}'>{$icon}</a>";
    }
}

if (!function_exists('decryption')) {
    /**
     * Decrypts the given encrypted data using AES-128-GCM.
     *
     * @param string $encData The encrypted data to decrypt.
     * @param string $token The token used for decryption.
     * @return string            The decrypted data.
     * @throws Exception
     */
    function decryption($encData, $token)
    {
        if (empty($encData)) {
            return '';
        }
        $key = substr($token, 0, 16);
        $cipher = 'aes-128-gcm';
        $tag_length = 16;
        $iv_len = openssl_cipher_iv_length($cipher);
        $iv = substr($encData, 0, $iv_len);
        $tag = substr($encData, -$tag_length);
        $ciphertext = substr($encData, $iv_len, -$tag_length);
        $decrypted = @openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($decrypted === false) {
            throw new Exception('failed decrypt');
        }
        return $decrypted;
    }
}


if (!function_exists('st')) {
    /**
     * Translate the given message
     * - first, will check the main project (without fallback) for translate
     * - second, will check in subsystem namespace
     * - third, if $namespace is given, will check in $namespace
     * - by default, the translation of the main project + fallback is return
     *
     * @param string $key
     * @param string $namespace namsespace used in package
     * @param array $replace
     * @param string $locale
     * @return string|array|null
     */
    function st($key, $replace = [], $namespace = 'app', $locale = null): array|string|null
    {
        $key = $namespace . '.' . $key;

        // check for translate , in main project (without fallback)
        $localTranslate = app('translator')->get($key, [], $locale, false);
        if ($key != $localTranslate) {
            return app('translator')->get($key, $replace, $locale, false);
        }

        foreach ($replace as $k => $v) {
            if ($namespace === 'validation') {
                $attribute = 'subsystem::validation.attributes.' . $v;
                $translatedValue = trans($attribute, [], $locale);
                if ($translatedValue != $attribute) {
                    $replace[$k] = $translatedValue;
                    continue;
                }
                $attribute = 'validation.attributes.' . $v;
                $translatedValue = trans($attribute, [], $locale);
                if ($translatedValue != $attribute) {
                    $replace[$k] = $translatedValue;
                }
            } else {
                $attribute = 'subsystem::' . $v;

                $translatedValue = trans($attribute, [], $locale);
                if ($translatedValue != $attribute) {
                    $replace[$k] = $translatedValue;
                }
            }
        }

        $subsystemTranslate = trans('subsystem::' . $key, $replace, $locale);
        if ('subsystem::' . $key !== $subsystemTranslate) {
            return $subsystemTranslate;
        }

        return strtr(trans($key, [], $locale), $replace);
    }
}

if (!function_exists('activeMenu')) {
    function activeMenu($routeName)
    {
        $active = [];
        $menu = config('subsystemMenu.' . $routeName);

        if ($menu['child']) {
            foreach ($menu['child'] as $items) {
                if (isset($items['active'])) {
                    foreach ($items['active'] as $activeItem) {
                        $active[] = $activeItem;
                    }
                }
            }
        }
        return array_unique($active);
    }
}

if (!function_exists('activeSubMenu')) {
    function activeSubMenu($routeName, $subRouteName)
    {
        return config('subsystemMenu.' . $routeName . '.child.' . $subRouteName . '.active');
    }
}

if (!function_exists('toJalaliDate')) {
    function toJalaliDate($date, $format = 'Y/m/d'): string
    {
        if (empty($date)) {
            return '';
        }
        return verta($date)->timezone('Asia/Tehran')->format($format);
    }
}

if (!function_exists('replaceAmount')) {
    function replaceAmount($val)
    {
        return $val ? intval(str_replace(',', '', $val)) : null;
    }
}

if (!function_exists('parseAmount')) {
    function parseAmount($val)
    {
        if (!$val) {
            return null;
        }
        $value = preg_replace('/[^0-9]/', '', $val);

        return preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $value);
    }
}

if (! function_exists('exchange')) {
    /**
     * Convert an amount from one currency to another.
     *
     * @example
     *  exchange(10000, 'IRR', 'IRT'); // returns 1000
     *  exchange(1, 'IRT', 'IRR');    // returns 10
     */
    function exchange(int $amount, string|UnitEnum $fromCurrency, string|UnitEnum $toCurrency): float|int|string
    {
        $currencyRates = [
            'IRR' => 1, // ریال به عنوان واحد پایه
            'IRT' => 10, // هر 1 تومان = 10 ریال
        ];

        if (empty($amount)) {
            return $amount;
        }
        $fromCurrency = $fromCurrency instanceof UnitEnum ? $fromCurrency->value : $fromCurrency;
        $toCurrency = $toCurrency instanceof UnitEnum ? $toCurrency->value : $toCurrency;

        if (! isset($currencyRates[$fromCurrency]) || ! isset($currencyRates[$toCurrency])) {
            throw new Exception("Currency conversion rate not defined for '{$fromCurrency}' to '{$toCurrency}'");
        }

        $amountConverted = $amount;
        if ($fromCurrency != $toCurrency) {
            $baseAmount = $amount * $currencyRates[$fromCurrency];
            $amountConverted = ceil($baseAmount / $currencyRates[$toCurrency]);
        }

        return (int) $amountConverted;
    }
}

if (!function_exists('getClientIP')) {
    /**
     * Retrieves the client's IP address from the server environment.
     *
     * This function checks multiple sources to determine the client's IP address:
     * - 'HTTP_CLIENT_IP': IP address from shared internet connections.
     * - 'HTTP_X_FORWARDED_FOR': IP address from proxy servers (can be a comma-separated list).
     * - 'REMOTE_ADDR': Direct IP address from the client connection.
     * - 'HTTP_X_REAL_IP': IP address in case of special configurations.
     *
     * @return string The resolved IP address, or 'Unknown IP' if none is found.
     */
    function getClientIP(): string
    {
        if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP']; // IP from shared internet
        } elseif (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // IP from proxies, could be a comma-separated list
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } elseif (! empty($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP']; // IP from remote address
        } elseif (! empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR']; // IP from remote address
        }
        return 'Unknown IP';
    }
}
