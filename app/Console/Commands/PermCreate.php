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
        $router = app('router');
        $routes = $router->getRoutes()->getRoutes();

        $routeData = [];
        $adminNameSpace = 'App\Http\Controllers\Admin';
        foreach ($routes as $route) {
            if ($route->getAction('namespace') == $adminNameSpace) {
                $controller = $route->getAction('controller');
                $controller = str_replace($adminNameSpace, '', $controller);
                $controller = ltrim($controller, '\\');
                $shortController = explode('@', $controller)[0];
                $shortController = str_replace('Controller', '', $shortController);
                $action = explode('@', $controller)[1];

                $shortController = snake_case($shortController);
                $action = snake_case($action);

                $routeData[] = [
                    'name' => $shortController . '|' . $action,
                    'guard_name' => 'admin',
                ];
            }
        }

        foreach ($routeData as $item) {
            Permission::updateOrCreate(['name' => $item['name']], $item);
        }
    }
}
