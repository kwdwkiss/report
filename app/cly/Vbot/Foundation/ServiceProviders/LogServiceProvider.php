<?php

namespace Cly\Vbot\Foundation\ServiceProviders;

use Cly\Vbot\Foundation\ServiceProviderInterface;
use Cly\Vbot\Foundation\Vbot;
use Cly\Vbot\Support\Log;

class LogServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $vbot)
    {
        $vbot->singleton('log', function () use ($vbot) {
            $log = new Log('vbot');

            return $log;
        });

        $vbot->singleton('messageLog', function () use ($vbot) {
            $log = new Log('message');

            return $log;
        });
    }
}
