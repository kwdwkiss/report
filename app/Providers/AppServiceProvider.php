<?php

namespace App\Providers;

use Aliyun\Oss;
use Aliyun\Sms;
use Cly\Session\SessionGuard;
use Cly\Vbot\VbotService;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Schema::defaultStringLength(191);

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

        //配置后台管理员登录，可以访问horizon
        Horizon::auth(function ($request) {
            return \Auth::guard('admin')->check();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //注入短信服务
        $this->app->singleton('aliyun.sms', function () {
            $accessKeyId = env('ALIYUN_SMS_ACCESS_KEY_ID');
            $accessKeySecret = env('ALIYUN_SMS_ACCESS_KEY_SECRET');
            $signName = env('ALIYUN_SMS_SIGN_NAME');
            $templateCode = env('ALIYUN_SMS_TEMPLATE_CODE');
            return new Sms($accessKeyId, $accessKeySecret, $signName, $templateCode);
        });

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
}
