<?php

namespace Alyani\Subsystem\Traits;

use Illuminate\Support\Facades\Validator;

trait ExtendValidationRule
{
    /**
     * Boot rules
     */
    public function bootValidationRules(): void
    {
        $this->registerRule($this->rules);
        $this->registerRule($this->dependentRules, 'dependent');
        $this->registerRule($this->implicitRules, 'implicit');
    }

    /**
     * Register validation rule based on simple, dependent, implicit types
     * @var array $rules
     * @var string $type [dependent, implicit]
     */
    protected function registerRule(array $rules, $type = ''): void
    {
        foreach ($rules as $class) {
            // Get Alias and message form class
            $classObject = (new $class());
            $alias = $classObject->__toString();
            $message = '';
            if (method_exists($classObject, 'message')) {
                $message = $classObject->message();
            }
            if ( !$alias) {
                continue;
            }

            // Register extention rule
            match ($type) {
                'dependent' => Validator::extendDependent($alias, $class . '@rule', $message),
                'implicit' => Validator::extendImplicit($alias, $class . '@rule', $message),
                default => Validator::extend($alias, $class . '@rule', $message),
            };
        }
    }
}
