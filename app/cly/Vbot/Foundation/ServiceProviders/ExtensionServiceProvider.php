<?php

namespace Cly\Vbot\Foundation\ServiceProviders;

use Cly\Vbot\Extension\MessageExtension;
use Cly\Vbot\Foundation\ServiceProviderInterface;
use Cly\Vbot\Foundation\Vbot;

class ExtensionServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $vbot)
    {
        $vbot->singleton('messageExtension', function () use ($vbot) {
            return new MessageExtension($vbot);
        });
    }
}
