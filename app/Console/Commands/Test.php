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
use Cly\Emulator\Pdd;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Dispatcher;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
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
        $path = storage_path('pid_files');
        $pid = pcntl_fork();
        if ($pid == -1) {
            die('could not fork');
        } elseif ($pid) {
            $pid = posix_getpid();
            $ppid = posix_getppid();
            $this->line("parent:$pid $ppid");
            //pcntl_wait($status);
        } else {
            $pid = posix_getpid();
            $ppid = posix_getppid();
            $this->line("child:$pid $ppid");
        }

//        \DB::enableQueryLog();
//        $today = \App\User::query()
//            ->whereDate('created_at', Carbon::today()->toDateString())
//            ->count();
//        dd(\DB::getQueryLog(), $today);
    }
}
