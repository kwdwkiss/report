<?php

namespace App\Console\Commands;

use App\Jobs\VbotUserClear;
use App\VbotJob;
use Cly\Vbot\Foundation\Vbot;
use Cly\Vbot\Message\FriendVerify;
use Cly\Vbot\Message\Text;
use Cly\Vbot\VbotService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class VbotCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vbot';

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
        $vbotJob = VbotJob::create([
            'user_id' => 1,
            'status' => 0
        ]);
        $vbotService = new VbotService($vbotJob);
        $vbotService->manager();
    }
}
