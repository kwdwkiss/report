<?php

namespace Cly\Vbot\Foundation\ServiceProviders;

use Cly\Vbot\Api\ApiHandler;
use Cly\Vbot\Api\Search;
use Cly\Vbot\Api\Send;
use Cly\Vbot\Foundation\ServiceProviderInterface;
use Cly\Vbot\Foundation\Vbot;

class ApiServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Cly\Vbot\Foundation\Vbot $vbot
     */
    public function register(Vbot $vbot)
    {
        $vbot->singleton('api', function () use ($vbot) {
            return new ApiHandler($vbot);
        });
        $vbot->singleton('apiSend', function () use ($vbot) {
            return new Send($vbot);
        });
        $vbot->singleton('apiSearch', function () use ($vbot) {
            return new Search($vbot);
        });
    }
}
