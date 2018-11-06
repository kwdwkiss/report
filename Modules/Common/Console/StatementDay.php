<?php

namespace Modules\Common\Console;

use Modules\Common\Entities\Statement;
use Carbon\Carbon;
use Illuminate\Console\Command;

class StatementDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statement_day {--start=} {date?}';

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
            $date = $date ?: Carbon::yesterday()->toDateString();
            Statement::day($date);
        } else {
            $startDate = Carbon::parse($start);
            $endDate = $date ? Carbon::parse($date) : Carbon::now();
            while ($startDate < $endDate) {
                $this->info('statement ' . $startDate->toDateString());
                Statement::day($startDate->toDateString());
                $startDate = $startDate->addDay();
            }
        }
    }
}
