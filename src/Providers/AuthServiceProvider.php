<?php

namespace Alyani\Subsystem\Providers;

use Illuminate\Support\ServiceProvider;
use Alyani\Subsystem\Models\Manager;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->configureGuard();
    }

    protected function configureGuard(): void
    {
        config([
            'auth.providers.managers' => array_merge([
                'driver' => 'eloquent',
                'model' => $this->getAuthModel(),
            ], config('auth.providers.managers', [])),
        ]);

        config([
            'auth.guards.web' => array_merge(
                config('auth.guards.web', []),
                [
                    'driver' => 'session',
                    'provider' => 'managers',
                ],
            ),
        ]);
    }

    protected function getAuthModel(): string
    {
        return config('subsystem.adminAuthModel', Manager::class);
    }
}
