<?php

namespace Cly\Vbot\Foundation\ServiceProviders;

use Cly\Vbot\Foundation\ServiceProviderInterface;
use Cly\Vbot\Foundation\Vbot;
use Cly\Vbot\Observers\BeforeMessageObserver;
use Cly\Vbot\Observers\ExitObserver;
use Cly\Vbot\Observers\FetchContactObserver;
use Cly\Vbot\Observers\LoginSuccessObserver;
use Cly\Vbot\Observers\NeedActivateObserver;
use Cly\Vbot\Observers\Observer;
use Cly\Vbot\Observers\QrCodeObserver;
use Cly\Vbot\Observers\ReLoginSuccessObserver;

class ObserverServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Cly\Vbot\Foundation\Vbot $vbot
     */
    public function register(Vbot $vbot)
    {
        $vbot->singleton('observer', function () use ($vbot) {
            return new Observer($vbot);
        });
        $vbot->singleton('qrCodeObserver', function () use ($vbot) {
            return new QrCodeObserver($vbot);
        });
        $vbot->singleton('loginSuccessObserver', function () use ($vbot) {
            return new LoginSuccessObserver($vbot);
        });
        $vbot->singleton('reLoginSuccessObserver', function () use ($vbot) {
            return new ReLoginSuccessObserver($vbot);
        });
        $vbot->singleton('exitObserver', function () use ($vbot) {
            return new ExitObserver($vbot);
        });
        $vbot->singleton('fetchContactObserver', function () use ($vbot) {
            return new FetchContactObserver($vbot);
        });
        $vbot->singleton('beforeMessageObserver', function () use ($vbot) {
            return new BeforeMessageObserver($vbot);
        });
        $vbot->singleton('needActivateObserver', function () use ($vbot) {
            return new NeedActivateObserver($vbot);
        });
    }
}
