<?php

namespace Alyani\Subsystem\Providers;

use Alyani\Subsystem\Console\Commands\CreateDatatableCommand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Monolog\Formatter\LineFormatter;

class SubsystemServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        $this->registerView();
        $this->registerPublishes();
        $this->registerRoutes();
        $this->registerLang();
        $this->log();

        $this->commands([
            CreateDatatableCommand::class,
        ]);
    }

    public function registerPublishes(): void
    {
        /**
         * migrations
         */
        $this->publishes([
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
            __DIR__ . '/../../database/seeders/SubsystemSeeder.php' => database_path('seeders/SubsystemSeeder.php'),
        ], 'subsystem');

        /**
         * config
         */
        $this->publishes([
            __DIR__ . '/../../config' => config_path(),
        ], 'subsystem');

        /**
         * resources/views
         */
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/subsystem/'),
        ], 'subsystem-views');

        /**
         * public
         */
        $this->publishes([
            __DIR__ . '/../../public' => public_path('vendor/subsystem'),
        ], 'subsystem');
    }

    public function registerLang(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'subsystem');
    }

    public function registerRoutes(): void
    {
        Route::middleware('web')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
            });

        Route::middleware('api')
            ->prefix('api')
            ->name('api.')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
            });
    }

    public function registerView(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'subsystem');
    }

    public function log()
    {
        if (!config('subsystem.sqlDebug', false)) {
            return;
        }

        $logger = Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/sql.log'),
            'formatter' => LineFormatter::class,
            'formatter_with' => [
                'format' => "%message% %context% %extra%\n",
                'allowInlineLineBreaks' => true,
                'ignoreEmptyContextAndExtra' => true,
            ],
        ]);

        DB::listen(fn($query) => $logger->info("[{$query->time}] " . $query->sql, $query->bindings));
    }
}
