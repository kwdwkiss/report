<?php

namespace Modules\Common\Entities;

use Illuminate\Routing\Route;

class Permission extends \Spatie\Permission\Models\Permission
{
    public static $guardTitle = [
        'admin' => '后台管理',
        'user' => '前台用户',
    ];

    public static function initCreate()
    {
        $router = app('router');
        $routes = $router->getRoutes()->getRoutes();

        $permIds = [];
        $namespaces = ['Modules\Admin\Http\Controllers' => 'admin'];
        $namespacesKeys = array_keys($namespaces);
        $middleware = 'rbac';

        foreach ($routes as $route) {
            $namespace = $route->getAction('namespace');
            $routeMiddlware = $route->gatherMiddleware();

            if (in_array($middleware, $routeMiddlware) && in_array($namespace, $namespacesKeys)) {

                $guard_name = $namespaces[$namespace];
                $perm = static::getPermFromRoute($route);
                $controller = explode('@', $perm)[0];
                $action = explode('@', $perm)[1];
                $routeName = $route->getName();

                $permission = static::updateOrCreate([
                    'name' => $perm,
                    'guard_name' => $guard_name,
                    'title' => $routeName,
                    'controller' => $controller,
                    'action' => $action,
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

        return $basename . '@' . $action;
    }
}
