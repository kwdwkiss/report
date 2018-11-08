<?php

namespace Modules\Common\Providers;

use Aliyun\Oss;
use Aliyun\Sms;
use Cly\Session\SessionGuard;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;
use Modules\Common\Entities\RechargeBill;
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

        \Schema::defaultStringLength(191);
        $this->bootSession();
        $this->bootHorizon();
        $this->bootMorphMap();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        $this->registerCommands();
        $this->registerMiddleware();
        $this->registerSms();
        $this->registerOss();
    }

    protected function bootMorphMap()
    {
        Relation::morphMap([
            1 => RechargeBill::class,
        ]);
    }

    protected function bootSession()
    {
        //原生的session，刷新会有bug
        \Auth::extend('cly_session', function ($app, $name, $config) {
            $provider = \Auth::createUserProvider($config['provider'] ?? null);

            $guard = new SessionGuard($name, $provider, $app['session.store']);

            // When using the remember me functionality of the authentication services we
            // will need to be set the encryption instance of the guard, which allows
            // secure, encrypted cookie values to get generated for those cookies.
            if (method_exists($guard, 'setCookieJar')) {
                $guard->setCookieJar($app['cookie']);
            }

            if (method_exists($guard, 'setDispatcher')) {
                $guard->setDispatcher($app['events']);
            }

            if (method_exists($guard, 'setRequest')) {
                $guard->setRequest($app->refresh('request', $guard, 'setRequest'));
            }

            return $guard;
        });
    }

    protected function bootHorizon()
    {
        //配置后台管理员登录，可以访问horizon
        Horizon::auth(function ($request) {
            return \Auth::guard('admin')->check();
        });
    }

    protected function registerSms()
    {
        //注入短信服务
        $this->app->singleton('aliyun.sms', function () {
            $accessKeyId = env('ALIYUN_SMS_ACCESS_KEY_ID');
            $accessKeySecret = env('ALIYUN_SMS_ACCESS_KEY_SECRET');
            $signName = env('ALIYUN_SMS_SIGN_NAME');
            $templateCode = env('ALIYUN_SMS_TEMPLATE_CODE');
            return new Sms($accessKeyId, $accessKeySecret, $signName, $templateCode);
        });
    }

    protected function registerOss()
    {
        //注入oss服务
        $this->app->singleton('aliyun.oss', function () {
            $accessKeyId = env('ALIYUN_OSS_ACCESS_KEY_ID');
            $accessKeySecret = env('ALIYUN_OSS_ACCESS_KEY_SECRET');
            $endpoint = env('ALIYUN_OSS_ENDPOINT');
            $bucket = env('ALIYUN_OSS_BUCKET');
            $oss = new Oss($accessKeyId, $accessKeySecret, $endpoint);
            $oss->setBucket($bucket);
            return $oss;
        });
    }

    protected function registerCommands()
    {
        $namespace = 'Modules\Common\Console\\';
        foreach (glob(__DIR__ . '/../Console/*.php') as $file) {
            $basename = basename($file, '.php');
            $commandClass = $namespace . $basename;
            if (class_exists($commandClass)) {
                $this->commands($commandClass);
            }
        }
    }

    protected function registerMiddleware()
    {
        $router = app('router');

        $router->aliasMiddleware('json.success', JsonSuccessResponse::class);
        $router->aliasMiddleware('user.api', UserApi::class);
        $router->aliasMiddleware('domain.check', DomainCheck::class);
        $router->aliasMiddleware('rbac', PermissionCheck::class);

        $router->pushMiddlewareToGroup('web', 'json.success');
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
