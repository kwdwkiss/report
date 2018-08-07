<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
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
        $data=explode(',', '117.181.234.248, 111.11.6.51')[0];
        $data = get_geo_str($data);
        dd($data);

        //        \DB::enableQueryLog();
//        $today = \App\User::query()
//            ->whereDate('created_at', Carbon::today()->toDateString())
    }
}
