<?php

namespace App\Console\Commands;

use App\Permission;
use Illuminate\Console\Command;

class PermCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'perm:create';

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
//        $router = app('router');
//        $routes = $router->getRoutes()->getRoutes();
//
//        $routeData = [];
//        foreach ($routes as $route) {
//            if (strpos(''))
//                var_dump($route->uri);
//        }

        $data = [
            ['id' => 1, 'name' => 'index/dashboard', 'guard_name' => 'admin'],
            ['id' => 2, 'name' => 'statement/index', 'guard_name' => 'admin'],
        ];

        foreach ($data as $item) {
            Permission::updateOrCreate(['id' => $item['id']], $item);
        }
    }
}
