<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        pcntl_signal(SIGCHLD, function ($pid) {
            echo "pid:$pid pcntl_waitpid";
            pcntl_waitpid($pid, $status, WNOHANG);
        });

        $pid = pcntl_fork();
        if ($pid == -1) {
            throw new \Exception('fork error');
        } elseif ($pid == 0) {
            sleep(1);

            $pid = pcntl_fork();
            if ($pid == -1) {
                throw new \Exception('fork error');
            } elseif ($pid == 0) {
                return;
            }
            echo "l2_pid:$pid\n";
            sleep(5);
            return;
        }
        echo "l1_pid:$pid\n";
        while (true) {
            pcntl_signal_dispatch();
        }
    }
}
