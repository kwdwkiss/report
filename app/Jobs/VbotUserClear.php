<?php

namespace App\Jobs;

use App\User;
use Cly\Vbot\VbotService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
        $user = User::findOrFail($this->vbotJob->user_id);

        $vbotService = new VbotService($user);

        $vbotService->userClear();
    }
}
