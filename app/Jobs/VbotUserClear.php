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

    public $timeout = 60 * 5;

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
        $vbotService = new VbotService($this->vbotJob);

        try {
            $vbotService->userClear();
        } catch (\Exception $e) {
            $vbotService->error();
        }
    }
}
