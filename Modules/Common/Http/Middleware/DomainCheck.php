<?php

namespace Modules\Common\Http\Middleware;

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
        $accessIp = env('SITE_ADMIN_ACCESS_IP');
        if ($accessIp) {
            $accessIp = explode(',', $accessIp);
            $clientIp = get_client_ip();
            if (!in_array($clientIp, $accessIp)) {
                abort(403);
            }
        }
        return $next($request);
    }
}
