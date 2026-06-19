<?php

namespace Alyani\Subsystem\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use libphonenumber\PhoneNumberUtil;

class Mobile implements CastsAttributes
{
    /**
     * Country code field
     */
    protected $countryCodeField;

    public function __construct($countryCodeField = 'countryCode')
    {
        $this->countryCodeField = $countryCodeField;
    }

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $countryCode = $attributes[$this->countryCodeField] ?? '';

        if (! $countryCode) {
            try {
                $phoneUtil = PhoneNumberUtil::getInstance();
                $phoneNumberObject = $phoneUtil->parse($value);
                $countryCode = $phoneNumberObject->getCountryCode();
            } catch (\Exception $e) {
                Log::debug('mobileCastingError', [
                    'mobile' => $value,
                    'countryCode' => $countryCode,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $value = (string) str_replace('+' . $countryCode, '', $value);
        return $value ?: null;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return (string) $value;
    }
}
