<?php

namespace App\Http\Middleware;

use App\Exceptions\JsonException;
use App\Permission;
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

//        if (!$admin->hasPermissionTo($permName)) {
//            throw new JsonException('没有权限');
//        }

        return $next($request);
    }
}
