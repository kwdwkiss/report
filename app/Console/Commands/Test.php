<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ko\Process;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $manager = new \Ko\ProcessManager();
        $manager->demonize();
        $exit = 0;
        $manager->onShutdown(function () use (&$exit, $manager) {
            $exit = 1;
        });

        $p = $manager->fork(function (Process $p) {
            $p->getSignalHandler()->registerHandler(SIGTERM, function () {
                echo 'child exit after 5 seconds' . PHP_EOL;
                sleep(5);
                echo 'child is sig exit' . PHP_EOL;
                //exit();
            });
//            while (true) {
//                if ($p->isShouldShutdown()) {
//                    exit();
//                }
//                $p->dispatch();
//                sleep(1);
//            }
            sleep(5);
            echo 'child exit' . time() . PHP_EOL;
            exit();
        });

        echo 'Execute `kill ' . getmypid() . '` from console to stop script' . PHP_EOL;
        $i = 0;
        while ($manager->hasAlive() || !$exit) {
//            if ($i == 8) {
//                echo 'send SIGTREM' . PHP_EOL;
//                $p->kill();
//            }
            $manager->dispatch();
            $i++;
            sleep(1);
        }
        echo 'manager exit';
        exit();
    }
}
