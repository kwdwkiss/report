<?php

namespace Modules\Common\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Common\Console\CleanTemp;
use Modules\Common\Console\FavorRestore;
use Modules\Common\Console\MigrateDown;
use Modules\Common\Console\MigrateUp;
use Modules\Common\Console\PermRefresh;
use Modules\Common\Console\ProductCreate;
use Modules\Common\Console\SearchBillDay;
use Modules\Common\Console\SearchBillMonth;
use Modules\Common\Console\StatementDay;
use Modules\Common\Console\StatementMonth;
use Modules\Common\Console\StatementProfile;
use Modules\Common\Console\Statics;
use Modules\Common\Console\UserAuthCheck;
use Modules\Common\Console\UserProductCheck;
use Modules\Common\Console\VbotCmd;
use Modules\Common\Console\VbotDeamon;
use Modules\Common\Http\Middleware\DomainCheck;
use Modules\Common\Http\Middleware\JsonSuccessResponse;
use Modules\Common\Http\Middleware\PermissionCheck;
use Modules\Common\Http\Middleware\UserApi;

class CommonServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        $this->commands([
            CleanTemp::class,
            FavorRestore::class,
            MigrateDown::class,
            MigrateUp::class,
            PermRefresh::class,
            ProductCreate::class,
            SearchBillDay::class,
            SearchBillMonth::class,
            StatementDay::class,
            StatementMonth::class,
            StatementProfile::class,
            Statics::class,
            UserAuthCheck::class,
            UserProductCheck::class,
            VbotCmd::class,
            VbotDeamon::class,
        ]);

        app('router')->aliasMiddleware('json.success', JsonSuccessResponse::class);
        app('router')->aliasMiddleware('user.api', UserApi::class);
        app('router')->aliasMiddleware('domain.check', DomainCheck::class);
        app('router')->aliasMiddleware('rbac', PermissionCheck::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('common.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php', 'common'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/common');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/common';
        }, \Config::get('view.paths')), [$sourcePath]), 'common');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/common');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'common');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'common');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (!app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
