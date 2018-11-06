<?php

namespace Modules\Common\Console;

use Modules\Common\Entities\SearchBill;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SearchBillMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search_bill_month {--start=} {date?}';

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
        $start = $this->option('start');
        $date = $this->argument('date');

        if (!$start) {
            $date = $date ?: Carbon::now()->subMonth()->format('Y-m');
            SearchBill::settleMonth($date);
        } else {
            $startDate = Carbon::parse($start)->format('Y-m');
            $endDate = $date ? Carbon::parse($date)->format('Y-m') : Carbon::now()->format('Y-m');
            while ($startDate < $endDate) {
                $this->info('statement ' . $startDate);
                SearchBill::settleMonth($startDate);
                $startDate = Carbon::parse($startDate)->addMonth()->format('Y-m');
            }
        }
    }
}
