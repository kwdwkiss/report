<?php

namespace App\Http\Middleware;

use Closure;

class DomainCheck
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
        $domain = env('SITE_ADMIN_DOMAIN');
        if ($domain && $domain != $request->getHost()) {
            abort(403);
        }
        return $next($request);
    }
}
