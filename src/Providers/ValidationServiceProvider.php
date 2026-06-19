<?php

namespace Alyani\Subsystem\Providers;

use Illuminate\Support\ServiceProvider;
use Alyani\Subsystem\Traits\ExtendValidationRule;

class ValidationServiceProvider extends ServiceProvider
{
    use ExtendValidationRule;

    /**
     * Rules
     */
    protected array $rules = [
        \Alyani\Subsystem\Rules\ValidMobile::class,
        \Alyani\Subsystem\Rules\ValidNationalCode::class,
        \Alyani\Subsystem\Rules\MobileCountryCode::class,
        \Alyani\Subsystem\Rules\English::class,
    ];

    /**
     * Dependent rules
     */
    protected array $dependentRules = [
        //
    ];

    /**
     * Implicit rules
     */
    protected array $implicitRules = [
        //
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootValidationRules();
    }
}
