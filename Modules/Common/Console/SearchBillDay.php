<?php

namespace Modules\Common\Console;

use Modules\Common\Entities\SearchBill;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SearchBillDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search_bill_day {--start=} {date?}';

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

        SearchBill::settleDay($date);
    }
}
