<?php

namespace App\Jobs;

use Cly\Vbot\VbotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VbotUserClear implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $vbotJob;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($vbotJob)
    {
        $this->onQueue('vbot_user_clear');
        $this->vbotJob = $vbotJob;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            throw new \Exception('could not fork');
        } elseif ($pid == 0) {
            $vbotService = new VbotService($this->vbotJob);
            try {
                $vbotService->userClear();
            } catch (\Exception $e) {
                $vbotService->error();
            }
        }
    }
}
