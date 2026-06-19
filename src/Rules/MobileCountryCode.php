<?php

namespace Alyani\Subsystem\Rules;

use Illuminate\Support\Arr;

/**
 * Check country_code against mobile
 * use mobileCountryCode:country_code
 */
class MobileCountryCode
{
    protected $alias = 'mobileCountryCode';

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute the name of the attribute being validated
     * @param string $value the value of the attribute
     * @param array $parameters an array of parameters passed to the rule
     *          [0] => mobile field name
     * @param \Illuminate\Validation\Validator $validator validator instance
     * @return bool
     */
    public function rule($attribute, $value, $parameters, $validator): bool
    {
        $data = Arr::dot($validator->getData());
        $mobile = $data[$attribute] ?? '';
        $country_code = $data[$parameters[0]] ?? '';

        // check if country_code is numeric
        if (empty($country_code) || !is_numeric($country_code)) {
            return false;
        }

        // check if mobile is numeric
        if (empty($mobile) || !is_numeric($mobile)) {
            return false;
        }

        if ( !str_starts_with($mobile, '+' . $country_code)) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message(): string
    {
        return __('subsystem::validation.mobile_country_code');
    }

    public function __toString()
    {
        return $this->alias;
    }
}
