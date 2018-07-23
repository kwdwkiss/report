<?php

namespace Cly\Vbot\Foundation\ServiceProviders;

use Cly\Vbot\Foundation\ServiceProviderInterface;
use Cly\Vbot\Foundation\Vbot;
use Cly\Vbot\Support\Http;

class HttpServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Cly\Vbot\Foundation\Vbot $vbot
     */
    public function register(Vbot $vbot)
    {
        $vbot->singleton('http', function () use ($vbot) {
            return new Http($vbot);
        });
    }
}
