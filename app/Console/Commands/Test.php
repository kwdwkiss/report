<?php

namespace App\Console\Commands;

use App\Http\Resources\VbotJobResource;
use App\VbotJob;
use Cly\Vbot\Foundation\Vbot;
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
        $data = VbotJob::find(14);
        //$data = new VbotJobResource($data);
        dd(count($data->friends));

        //        \DB::enableQueryLog();
//        $today = \App\User::query()
//            ->whereDate('created_at', Carbon::today()->toDateString())
    }
}
