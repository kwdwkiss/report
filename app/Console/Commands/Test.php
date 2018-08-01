<?php

namespace App\Console\Commands;

use App\Jobs\SendNotification;
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
        $childs = array();

// Fork some process.
        for ($i = 0; $i < 10; $i++) {
            $pid = pcntl_fork();
            if ($pid == -1)
                die('Could not fork');

            if ($pid) {
                echo "parent $i $pid\n";
                $childs[] = $pid;
            } else {
                $pid = posix_getpid();
                echo "child $pid\n";
                // Sleep $i+1 (s). The child process can get this parameters($i).
                sleep($i + 1);

                // The child process needed to end the loop.
                exit();
            }
        }

        while (count($childs) > 0) {
            foreach ($childs as $key => $pid) {
                $res = pcntl_waitpid($pid, $status, WNOHANG);

                echo "wait $pid $status $res";
                // If the process has already exited
                if ($res == -1 || $res > 0)
                    unset($childs[$key]);
            }

            sleep(1);
        }

//        \DB::enableQueryLog();
//        $today = \App\User::query()
//            ->whereDate('created_at', Carbon::today()->toDateString())
//            ->count();
//        dd(\DB::getQueryLog(), $today);
    }
}
