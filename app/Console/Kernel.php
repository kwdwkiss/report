<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('statics')->everyTenMinutes();
        $schedule->command('statement_profile')->everyTenMinutes();
        $schedule->command('statement_day')->dailyAt('00:00');
        $schedule->command('statement_month')->monthlyOn(1, '00:01');
        $schedule->command('search_bill_month')->monthlyOn(1, '02:10');
        //过期检查
        $schedule->command('user_product:check')->dailyAt('01:00');
        $schedule->command('user_auth:check')->dailyAt('01:01');
        //耗时任务2点执行
        $schedule->command('search_bill_day')->dailyAt('02:00');
        //清除temp文件
        $schedule->command('clean_temp')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
