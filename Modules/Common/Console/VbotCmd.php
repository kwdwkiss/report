<?php

namespace Modules\Common\Console;

use Modules\Common\Entities\VbotJob;
use Illuminate\Console\Command;

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
        \Cly\Vbot\VbotDeamon::sendVbotJob($vbotJob);
    }
}
