<?php

namespace App\Console\Commands;

use App\AccountSearch;
use App\Excel;
use App\RechargeBill;
use App\Statement;
use Carbon\Carbon;
use Cly\Process\Manager;
use Cly\Process\Process;
use Cly\RegExp\RegExp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use ipip\datx\City;
use Workerman\Worker;

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
        $data = preg_match(RegExp::PASSWORD, 'caoleiyu27');
        dd($data);
        //$this->processTest();
    }

    protected function processTest()
    {
        $manager = new Manager(['redis' => 'vbot', 'prefix' => 'l1', 'name' => '1']);
        $manager->setCallable(function (Manager $manager) {
            $subManager = new Manager(['redis' => 'vbot', 'prefix' => 'l2', 'name' => '1']);
            $subManager->setCallable(function (Manager $manager) {

                $process1 = new Process(['redis' => 'vbot', 'prefix' => 'l3', 'name' => '1']);
                $process1->setCallable(function ($process) {
                    while (true) {
                        sleep(1);
                    }
                });
                $manager->fork($process1);

                $process2 = new Process(['redis' => 'vbot', 'prefix' => 'l3', 'name' => '2']);
                $process2->setCallable(function ($process) {
                    while (true) {
                        sleep(1);
                    }
                });
                $manager->fork($process2);

                while (true) {
                    sleep(1);
                }
            });
            $manager->fork($subManager);

            while (true) {
                //echo 'sleep start: ' . time() . PHP_EOL;
                sleep(10);
                //echo 'sleep end  : ' . time() . PHP_EOL;
            }
        });
        $manager->run();
    }

    protected function enableQueryLog()
    {
        DB::enableQueryLog();
    }
}
