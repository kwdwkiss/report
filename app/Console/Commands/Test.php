<?php

namespace App\Console\Commands;

use Aliyun\Sms;
use App\Account;
use App\AccountReport;
use App\Admin;
use App\AmountBill;
use App\Attachment;
use App\Config;
use App\Jobs\SendNotification;
use App\Message;
use App\Notifications\SiteMessage;
use App\Tag;
use App\Taxonomy;
use App\UserProfile;
use Carbon\Carbon;
use Illuminate\Bus\Dispatcher;
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
        $userIds = AmountBill::select('user_id')->where('biz_type', 0)->where('biz_id', 1)->get()->pluck('user_id');
        $users = \App\User::with('_amountBill', '_profile')->whereNotIn('id', $userIds)->count();
        $result = $users;
        var_dump($result);
        $temp = '';
    }
}
