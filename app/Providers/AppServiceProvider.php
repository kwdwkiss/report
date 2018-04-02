<?php

namespace App\Providers;

use Aliyun\Oss;
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
