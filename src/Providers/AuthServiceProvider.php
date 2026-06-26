<?php

namespace Alyani\Subsystem\Providers;

use Alyani\Subsystem\Models\Manager;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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

    public function boot(): void
    {
        Route::macro('permission', function (string|array ...$permissions) {
            $permissions = collect($permissions)
                ->flatten()
                ->values()
                ->all();

            /** @var \Illuminate\Routing\Route $this */
            $this->action['permissions'] = $permissions;
            return $this;
        });

        // اگر می‌خواهید یک رول مثل Super Admin به همه چیز دسترسی داشته باشد:
        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasRole') && $user->hasRole('Super Admin')) {
                return true;
            }
        });
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
