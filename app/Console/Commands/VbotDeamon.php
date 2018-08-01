<?php

namespace App\Console\Commands;

use App\VbotJob;
use Cly\Vbot\VbotService;
use Illuminate\Console\Command;

class VbotDeamon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vbot:deamon';

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
            pcntl_waitpid($pid, $status, WNOHANG);
        });

        while (true) {
            $vbotJob = VbotJob::query()
                ->where('status', 0)
                ->first();
            if ($vbotJob) {
                $pid = pcntl_fork();
                if ($pid == -1) {
                    throw new \Exception('could not fork');
                } elseif ($pid) {
                    //parent
                    pcntl_signal_dispatch();
                    sleep(1);
                } else {
                    $vbotJob->update(['status' => 10]);
                    $vbotService = new VbotService($vbotJob);
                    try {
                        $vbotService->userClear();
                    } catch (\Exception $e) {
                        $vbotService->error();
                    }
                    break;
                }
            }
        }
    }
}
