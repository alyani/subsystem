<?php

namespace Alyani\Subsystem\Rules;

/**
 * Validate age limit
 */
class English
{
    protected $alias = 'englishOnly';

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute the name of the attribute being validated
     * @param string $value the value of the attribute
     * @param array $parameters an array of parameters passed to the rule
     *          [0] => min
     *          [1] => max
     * @param \Illuminate\Validation\Validator $validator validator instance
     * @return bool
     */
    public function rule($attribute, $value, $parameters, $validator): bool
    {
        return preg_match('/^[A-Za-z0-9 _-]+$/', $value);
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message(): string
    {
        return __('subsystem::validation.english_only');
    }

    public function __toString()
    {
        return $this->alias;
    }
}
