<?php

namespace Cly\Vbot\Foundation\ServiceProviders;

use Cly\Vbot\Foundation\ExceptionHandler;
use Cly\Vbot\Foundation\ServiceProviderInterface;
use Cly\Vbot\Foundation\Vbot;

class ExceptionServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Cly\Vbot\Foundation\Vbot $vbot
     */
    public function register(Vbot $vbot)
    {
        $vbot->singleton('exception', function () use ($vbot) {
            return new ExceptionHandler($vbot);
        });
    }
}
