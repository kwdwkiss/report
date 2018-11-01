<?php

namespace App;

use Illuminate\Routing\Route;

class Permission extends \Spatie\Permission\Models\Permission
{
    public static function initCreate()
    {
        $router = app('router');
        $routes = $router->getRoutes()->getRoutes();

        $permIds = [];
        $namespaces = ['App\Http\Controllers\Admin' => 'admin'];
        $namespacesKeys = array_keys($namespaces);

        foreach ($routes as $route) {
            $namespace = $route->getAction('namespace');
            if (in_array($namespace, $namespacesKeys)) {

                $guard_name = $namespaces[$namespace];
                $perm = static::getPermFromRoute($route);

                $permission = static::updateOrCreate([
                    'name' => $perm,
                    'guard_name' => $guard_name,
                ]);

                $permIds[] = $permission->id;
            }
        }

        Permission::query()->whereNotIn('id', $permIds)->delete();
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
