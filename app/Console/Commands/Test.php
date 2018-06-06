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
use App\RechargeBill;
use App\SearchBill;
use App\Tag;
use App\Taxonomy;
use App\UserProfile;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Dispatcher;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

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
        $data = mb_ereg_replace(mb_substr('桂林鸡血石', -2), '**', '桂林鸡血石');
        $data = str_pad('', 3, '*');
        dd($data);
//        \DB::enableQueryLog();
//        $today = \App\User::query()
//            ->whereDate('created_at', Carbon::today()->toDateString())
//            ->count();
//        dd(\DB::getQueryLog(), $today);
    }
}
