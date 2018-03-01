<?php

namespace App\Console\Commands;

use App\Account;
use App\AccountReport;
use App\Admin;
use App\Config;
use App\Tag;
use App\Taxonomy;
use App\UserProfile;
use Illuminate\Console\Command;

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
        var_dump(preg_match('/^[a-zA-Z]{1}[-_a-zA-Z0-9]{5,19}+$/', 'kwdwkiss'));
    }
}
