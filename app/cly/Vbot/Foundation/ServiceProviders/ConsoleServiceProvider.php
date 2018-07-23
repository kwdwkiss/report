<?php

namespace Cly\Vbot\Foundation\ServiceProviders;

use Cly\Vbot\Console\Console;
use Cly\Vbot\Console\QrCode;
use Cly\Vbot\Foundation\ServiceProviderInterface;
use Cly\Vbot\Foundation\Vbot;

class ConsoleServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $vbot)
    {
        $vbot->bind('qrCode', function () use ($vbot) {
            return new QrCode($vbot);
        });
        $vbot->singleton('console', function () use ($vbot) {
            return new Console($vbot);
        });
    }
}
