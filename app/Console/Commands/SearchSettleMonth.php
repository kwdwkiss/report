<?php

namespace App\Console\Commands;

use App\SearchBill;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SearchSettleMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search_settle_month {--start=} {date?}';

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
            $date = $date ?: Carbon::now()->subMonth()->firstOfMonth()->toDateString();
            SearchBill::settleMonth($date);
        } else {
            $startDate = Carbon::parse($start);
            $endDate = Carbon::parse($date);
            while ($startDate < $endDate) {
                $this->info('statement ' . date('Y-m', $startDate->getTimestamp()));
                SearchBill::settleMonth(date('Y-m', $startDate->getTimestamp()));
                $startDate = $startDate->addMonth();
            }
        }
    }
}
