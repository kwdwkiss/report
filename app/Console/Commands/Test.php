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
        $client = new Client();
        $api_key = 'e21a7715c88c06d5f0846052d60779c9';
        $api_secret = '7779413d6708f6a9c3ac239b16c4f64d';
        $params['name']='kwdwkiss';
        $params['api_key'] = $api_key;
        $params['timestamp'] = time();
        ksort($params);
        $sign = md5(http_build_query($params) . $api_secret);
        $params['sign'] = $sign;
        $res = $client->get('http://tbpzw.cly/user_api/account-report/search', [
            'query' => $params
        ]);
        dd($res->getBody()->getContents());
//        \DB::enableQueryLog();
//        $today = \App\User::query()
//            ->whereDate('created_at', Carbon::today()->toDateString())
//            ->count();
//        dd(\DB::getQueryLog(), $today);
    }
}
