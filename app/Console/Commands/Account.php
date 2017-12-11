<?php

namespace App\Console\Commands;

use App\Taxonomy;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Account extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account {startId=0}';

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
        $startId = $this->argument('startId');
        $conn = \DB::connection('origin');

        $statusData = [
            1 => 102,
            2 => 103,
            3 => 104,
            5 => 105,
            7 => 106
        ];

        $total = $conn->table('ims_account')->count();
        $pageSize = 3000;
        $pageCount = ceil($total / $pageSize);
        echo "total:$total pageSize:$pageSize pageCount:$pageCount\n";
        for ($i = 0; $i < $pageCount; $i++) {
            $rows = $conn->table('ims_account')->offset($i * $pageSize)->limit($pageSize)->get();
            \DB::transaction(function () use ($i, $rows, $statusData) {
                $num = 0;
                foreach ($rows as $row) {
                    $name = trim($row->account);
                    $type = is_numeric($name) ? 201 : 202;
                    $status = $statusData[$row->status_id];
                    $remark = $row->remark;
                    $address = $row->address;
                    $report_count = $row->report_count;
                    $auth_cash = $row->auth_cash;
                    if ($row->auth_time != 0) {
                        $auth_at = Carbon::createFromTimestamp($row->auth_time);
                    } else {
                        $auth_at = null;
                    }

                    $account = \App\Account::where('name', $name)->where('type', $type)->first();
                    if ($account) {
                        continue;
                    }

                    $account = new \App\Account();
                    $account->name = $name;
                    $account->type = $type;
                    $account->status = $status;
                    $account->remark = $remark;
                    $account->address = $address;
                    $account->report_count = $report_count;
                    $account->auth_at = $auth_at;
                    $account->auth_cash = $auth_cash;
                    $account->save();

                    echo "$i-$num $name $type $status $remark $address $report_count $auth_at $auth_cash\n";
                    $num++;
                }
            });
        }
    }
}
