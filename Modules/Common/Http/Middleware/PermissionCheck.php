<?php

namespace Modules\Common\Http\Middleware;

use Modules\Common\Entities\Permission;
use Modules\Common\Exceptions\JsonException;
use Closure;

class PermissionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = app('router')->getCurrentRoute();

        $permName = Permission::getPermFromRoute($route);

        $admin = \Auth::guard('admin')->user();
        $permission = Permission::findByName($permName);

        if (!$admin->hasPermissionTo($permission)) {
            throw new JsonException('你没有权限：' . $permission->title);
        }

        return $next($request);
    }
}
