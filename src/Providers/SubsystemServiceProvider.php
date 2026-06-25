<?php

namespace Alyani\Subsystem\Providers;

use Alyani\Subsystem\Console\Commands\CreateDataTableCommand;
use Alyani\Subsystem\Http\Middleware\CheckPermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
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

        
        config([
            'permission.models.permission' => \Spatie\Permission\Models\Permission::class,
            'permission.models.role' => \Spatie\Permission\Models\Role::class,
        ]);


        $this->registerView();
        $this->registerPublishes();
        $this->registerRoutes();
        $this->registerLang();
        $this->registerMiddleware();
        $this->log();

        $this->commands([
            CreateDataTableCommand::class,
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
            __DIR__ . '/../../config/captcha.php' => config_path('captcha.php'),
            __DIR__ . '/../../config/smsService.php' => config_path('smsService.php'),
            __DIR__ . '/../../config/subsystem.php' => config_path('subsystem.php'),
            __DIR__ . '/../../config/subsystemMenu.php' => config_path('subsystemMenu.php'),
            __DIR__ . '/../../config/subsystemPermissions-stub.php' => config_path('subsystemPermissions.php'),
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

    public function registerPermissions(): void
    {
        $packageConfigPath = __DIR__ . '/../../config/subsystemPermissions.php';

        if ($this->app->runningInConsole() || !config()->has('subsystemPermissions')) {
            $this->mergeConfigFrom($packageConfigPath, 'subsystemPermissions');
        } else {
            // اگر کاربر فایل پروژه را پاپلیش کرده و تغییر داده بود:
            $packagePermissions = require $packageConfigPath;
            $projectPermissions = config('subsystemPermissions', []);
            config([
                'permissions' => array_replace_recursive(
                    $packagePermissions,
                    $projectPermissions
                )
            ]);
        }
    }

    protected function registerMiddleware(): void
    {
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('checkPermission', CheckPermission::class);

        // میدل‌ورهای اسپاتی برای استفاده راحت در روت‌های پکیج یا پروژه
        $router->aliasMiddleware('role', \Spatie\Permission\Middleware\RoleMiddleware::class);
        $router->aliasMiddleware('permission', \Spatie\Permission\Middleware\PermissionMiddleware::class);
        $router->aliasMiddleware('role_or_permission', \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class);
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
