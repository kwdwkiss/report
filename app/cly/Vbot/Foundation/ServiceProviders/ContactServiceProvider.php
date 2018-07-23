<?php

namespace Cly\Vbot\Foundation\ServiceProviders;

use Cly\Vbot\Contact\Contacts;
use Cly\Vbot\Contact\Friends;
use Cly\Vbot\Contact\Groups;
use Cly\Vbot\Contact\Members;
use Cly\Vbot\Contact\Myself;
use Cly\Vbot\Contact\Officials;
use Cly\Vbot\Contact\Specials;
use Cly\Vbot\Core\ContactFactory;
use Cly\Vbot\Foundation\ServiceProviderInterface;
use Cly\Vbot\Foundation\Vbot;

class ContactServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $vbot)
    {
        $vbot->bind('contactFactory', function () use ($vbot) {
            return new ContactFactory($vbot);
        });
        $vbot->singleton('myself', function () use ($vbot) {
            return new Myself();
        });
        $vbot->singleton('friends', function () use ($vbot) {
            return (new Friends())->setVbot($vbot);
        });
        $vbot->singleton('groups', function () use ($vbot) {
            return (new Groups())->setVbot($vbot);
        });
        $vbot->singleton('members', function () use ($vbot) {
            return (new Members())->setVbot($vbot);
        });
        $vbot->singleton('officials', function () use ($vbot) {
            return (new Officials())->setVbot($vbot);
        });
        $vbot->singleton('specials', function () use ($vbot) {
            return (new Specials())->setVbot($vbot);
        });
        $vbot->singleton('contacts', function () use ($vbot) {
            return (new Contacts())->setVbot($vbot);
        });
    }
}
