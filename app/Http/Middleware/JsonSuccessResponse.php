<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class JsonSuccessResponse
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
        $response = $next($request);
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            $response->setData(array_merge([
                'message' => '成功',
                'code' => 0
            ], $data));
        }
        return $response;
    }
}
