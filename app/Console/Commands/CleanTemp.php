<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class CleanTemp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean_temp';

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
        $finder = Finder::create()->files()->in(storage_path('app/public/temp'));
        foreach ($finder as $file) {
            $carbon = Carbon::createFromTimestamp($file->getMTime());
            if (Carbon::now()->diffInMinutes($carbon) > 60) {
                unlink($file);
            }
        }
    }
}
