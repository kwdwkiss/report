<?php

namespace App\Console\Commands;

use Aliyun\Sms;
use App\Account;
use App\AccountReport;
use App\Admin;
use App\Config;
use App\Tag;
use App\Taxonomy;
use App\UserProfile;
use Carbon\Carbon;
use Illuminate\Console\Command;

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
        //var_dump(preg_match('/^1(3[0-9]|4[579]|5[0-35-9]|7[0-9]|8[0-9])\d{8}$/', 18677303808));
        $result = Carbon::now()->startOfMonth();
        var_dump($result);
        $temp = '';
    }
}
