<?php

namespace Cly\Vbot\Core;

use Cly\Vbot\Console\Console;
use Cly\Vbot\Exceptions\ApiFrequencyException;
use Cly\Vbot\Exceptions\ArgumentException;
use Cly\Vbot\Exceptions\CookiesInvalidException;
use Cly\Vbot\Exceptions\LogoutException;
use Cly\Vbot\Exceptions\TicketErrorException;

class ApiExceptionHandler
{
    public static function handle($bag, $callback = null)
    {
        if ($callback && !is_callable($callback)) {
            throw new ArgumentException();
        }

        if ($bag['BaseResponse']['Ret'] != 0) {
            if ($callback) {
                call_user_func_array($callback, $bag);
            }
        }

        switch ($bag['BaseResponse']['Ret']) {
            case 1:
                vbot('console')->log('Argument pass error.', Console::WARNING);
                throw new ArgumentException('Argument pass error.');
                break;
            case -14:
                vbot('console')->log('Ticket error.', Console::WARNING);
                throw new TicketErrorException('Ticket error.');
                break;
            case 1100:
                vbot('console')->log('Logout.', Console::WARNING);
                throw new LogoutException('Logout.');
                break;
            case 1101:
                vbot('console')->log('Logout.', Console::WARNING);
                throw new LogoutException('Logout.');
                break;
            case 1102:
                vbot('console')->log('Cookies invalid.', Console::WARNING);
                throw new CookiesInvalidException('Cookies invalid.');
                break;
            case 1105:
                vbot('console')->log('Api frequency.', Console::WARNING);
                throw new ApiFrequencyException('Api frequency.');
                break;
        }

        return $bag;
    }
}
