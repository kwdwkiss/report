<?php

namespace App\Providers;

use Aliyun\Sms;
use Illuminate\Support\ServiceProvider;

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
    }
}
