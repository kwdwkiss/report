<?php

namespace App\Console\Commands;

use App\UserProfile;
use Illuminate\Console\Command;

class FixUserProfile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:user_profile';

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
        \App\User::query()->with('_profile')->chunk(1000, function ($users) {
            foreach ($users as $user) {
                if (!$user->_profile) {
                    UserProfile::create([
                        'user_id' => $user->id
                    ]);
                }
            }
        });
    }
}
