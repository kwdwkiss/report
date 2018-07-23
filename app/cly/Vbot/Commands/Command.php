<?php

namespace Cly\Vbot\Commands;

use Symfony\Component\Console\Application;

/**
 * Class Command.
 */
class Command
{
    public function run()
    {
        $application = new Application();

        $application->run();
    }
}
