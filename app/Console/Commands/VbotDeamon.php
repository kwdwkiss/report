<?php

namespace App\Console\Commands;

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
        (new \Cly\Vbot\VbotDeamon())->run();
    }
}
