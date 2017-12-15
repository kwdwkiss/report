<?php

namespace App\Console\Commands;

use App\AccountReport;
use App\Taxonomy;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Report extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report {page=0} {num=0}';

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
        $startPage = $this->argument('page');
        $startNum = $this->argument('num');
        $conn = \DB::connection('origin');

        $data = $conn->table('ims_report_type')->get();
        $originReportType = array_combine(array_pluck($data, 'id'), array_pluck($data, 'name'));
        $data = Taxonomy::where('pid', Taxonomy::REPORT_TYPE)->get();
        $targetReportType = array_combine(array_pluck($data, 'name'), array_pluck($data, 'id'));

        $originTable = 'ims_report';
        $total = $conn->table($originTable)->count();
        $pageSize = 1000;
        $pageCount = ceil($total / $pageSize);
        echo "total:$total pageSize:$pageSize pageCount:$pageCount\n";

        \DB::statement('truncate table ' . (new AccountReport())->getTable());
        for ($i = $startPage; $i < $pageCount; $i++) {
            $rows = $conn->table($originTable)->offset($i * $pageSize)->limit($pageSize)->get();
            \DB::transaction(function () use ($startPage, $startNum, $i, $rows, $originReportType, $targetReportType) {
                if ($i == $startPage) {
                    $num = $startNum;
                } else {
                    $num = 0;
                }
                foreach ($rows as $row) {
                    $accountTypeId = is_numeric($row->account) ? 201 : 202;
                    $name = trim($row->account);
                    if ($row->type_id == 0) {
                        $reportTypeId = 310;
                    } else {
                        if (!isset($originReportType[$row->type_id])) {
                            continue;
                        }
                        $originReportTypeLabel = $originReportType[$row->type_id];
                        if ($originReportTypeLabel == '微信') {
                            $reportTypeId = 311;
                        } else {
                            $reportTypeId = $targetReportType[$originReportTypeLabel];
                        }

                    }
                    if ($row->report_ip == '') {
                        continue;
                    }
                    if (strpos($row->report_ip, ', ') !== false) {
                        $ip = explode(', ', $row->report_ip)[0];
                    } else {
                        $ip = $row->report_ip;
                    }
                    if (filter_var($row->report_ip, FILTER_VALIDATE_IP) === false) {
                        continue;
                    }
                    if ($row->time == 0) {
                        $row->time = 1406773340;
                    }
                    $updated_at = $created_at = Carbon::createFromTimestamp($row->time);

                    if ($accountTypeId == 201 && !preg_match('/^[1-9][0-9]{4,14}$/', $name)) {
                        continue;
                    }

                    $accountReport = new AccountReport();
                    $accountReport->account_type = $accountTypeId;
                    $accountReport->account_name = $name;
                    $accountReport->type = $reportTypeId;
                    $accountReport->ip = $ip;
                    $accountReport->remark = $row->remark;
                    $accountReport->created_at = $created_at;
                    $accountReport->updated_at = $updated_at;
                    $accountReport->timestamps = false;
                    $accountReport->save();

                    echo "$i-$num $name $accountTypeId $reportTypeId\n";
                    $num++;
                }
            });
        }
    }
}
