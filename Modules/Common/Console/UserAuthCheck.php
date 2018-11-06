<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Common\Entities\User;

class UserAuthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user_auth:check';

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
        User::query()
            ->where('auth_end_at', '<', Carbon::now())
            ->update(['type' => '401']);
    }
}
