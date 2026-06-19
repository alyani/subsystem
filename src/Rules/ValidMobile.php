<?php

namespace Alyani\Subsystem\Rules;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

/**
 * Validate mobile
 */
class ValidMobile
{
    protected $alias = 'validMobile';

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute the name of the attribute being validated
     * @param string $value the value of the attribute
     * @param array $parameters an array of parameters passed to the rule
     * @param \Illuminate\Validation\Validator $validator validator instance
     * @return bool
     */
    public function rule($attribute, $value, $parameters, $validator): bool
    {
        if (!is_numeric($value) || !is_string($value)) {
            return false;
        }
        if (substr($value, 0, 3) == "+98") {
            return preg_match('/^\+(989)\d{9}$/', $value);
        }
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $phoneNumberObject = $phoneUtil->parse($value);
        } catch (NumberParseException $e) {
            return false;
        }
        if (!$phoneUtil->isPossibleNumber($phoneNumberObject)) {
            return false;
        }
        if (!$phoneUtil->isValidNumber($phoneNumberObject)) {
            return false;
        }
        $numberType = $phoneUtil->getNumberType($phoneNumberObject);
        if (!in_array($numberType, [1, 2])) {
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
        return __('subsystem::validation.mobile');
    }

    public function __toString()
    {
        return $this->alias;
    }
}
