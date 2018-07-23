<?php

namespace Cly\Vbot\Foundation\ServiceProviders;

use Cly\Vbot\Core\Server;
use Cly\Vbot\Core\Swoole;
use Cly\Vbot\Core\Sync;
use Cly\Vbot\Foundation\ServiceProviderInterface;
use Cly\Vbot\Foundation\Vbot;

class ServerServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $vbot)
    {
        $vbot->singleton('server', function () use ($vbot) {
            return new Server($vbot);
        });
        $vbot->singleton('swoole', function () use ($vbot) {
            return new Swoole($vbot);
        });
        $vbot->singleton('sync', function () use ($vbot) {
            return new Sync($vbot);
        });
    }
}
