<?php

namespace App\Http\Middleware;

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

        $perm = Permission::getPermFromRoute($route);

        $admin = \Auth::guard('admin')->user();

        return $next($request);
    }
}
