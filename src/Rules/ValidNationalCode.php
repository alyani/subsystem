<?php

namespace Alyani\Subsystem\Rules;

/**
 * Validate national code
 */
class ValidNationalCode
{
    protected $alias = 'validNationalCode';

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
        if (empty($value) || !is_numeric($value)) {
            return false;
        }
        if (!preg_match("/^\d{10}$/", $value)) {
            return false;
        }
        $check = (int)$value[9];
        $sum = array_sum(array_map(function ($x) use ($value) {
                return ((int)$value[$x]) * (10 - $x);
            }, range(0, 8))) % 11;

        if (!($sum < 2 ? $check == $sum : $check + $sum == 11)) {
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
        return __('subsystem::validation.national_code');
    }

    public function __toString()
    {
        return $this->alias;
    }
}
