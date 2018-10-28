<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class RoleCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:create';

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
        $data = [
            [
                'id' => 1,
                'name' => '超级管理员',
                'guard_name' => 'admin',
            ],
            [
                'id' => 2,
                'name' => '客服',
                'guard_name' => 'admin',
            ],
        ];

        foreach ($data as $item) {
            Role::updateOrCreate([
                'id' => $item['id']
            ], $item);
        }
    }
}
