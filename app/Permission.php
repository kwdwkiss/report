<?php

namespace App;

use Illuminate\Routing\Route;

class Permission extends \Spatie\Permission\Models\Permission
{
    public static function initCreate()
    {
        $router = app('router');
        $routes = $router->getRoutes()->getRoutes();

        $routeData = [];
        $adminNameSpace = 'App\Http\Controllers\Admin';
        foreach ($routes as $route) {
            if ($route->getAction('namespace') == $adminNameSpace) {
                $perm = static::getPermFromRoute($route);
                $routeData[] = [
                    'name' => $perm,
                    'guard_name' => 'admin',
                ];
            }
        }

        foreach ($routeData as $item) {
            static::updateOrCreate(['name' => $item['name']], $item);
        }
    }

    public static function getPermFromRoute(Route $route)
    {
        $controller = $route->getAction('controller');
        $class = explode('@', $controller)[0];
        $action = explode('@', $controller)[1];

        $basename = class_basename($class);
        $basename = str_replace('Controller', '', $basename);

        $basename = snake_case($basename);
        $action = snake_case($action);

        return $basename . '|' . $action;
    }
}
