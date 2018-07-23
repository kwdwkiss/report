<?php

namespace Cly\Vbot\Foundation\ServiceProviders;

use Cly\Vbot\Core\MessageFactory;
use Cly\Vbot\Core\MessageHandler;
use Cly\Vbot\Core\ShareFactory;
use Cly\Vbot\Foundation\ServiceProviderInterface;
use Cly\Vbot\Foundation\Vbot;
use Cly\Vbot\Message\Text;

class MessageServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $vbot)
    {
        $vbot->singleton('messageHandler', function () use ($vbot) {
            return new MessageHandler($vbot);
        });
        $vbot->singleton('messageFactory', function () use ($vbot) {
            return new MessageFactory($vbot);
        });
        $vbot->singleton('shareFactory', function () use ($vbot) {
            return new ShareFactory($vbot);
        });

        //        $vbot->bind('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
    }
}
