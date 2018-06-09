<?php

namespace App\Http\Middleware;

use App\Exceptions\JsonException;
use Carbon\Carbon;
use Closure;

class UserApi
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
        $input = $request->input();

        $sign = array_get($input, 'sign');
        $api_key = array_get($input, 'api_key');
        $timestamp = array_get($input, 'timestamp');

        if (!$sign) {
            throw new JsonException('sign null', 1);
        }
        if (!$api_key) {
            throw new JsonException('api_key null', 2);
        }
        if (!$timestamp) {
            throw new JsonException('timestamp null', 3);
        }
        $time = Carbon::parse($timestamp);
        if ($time < Carbon::now()->subMinute() || $time > Carbon::now()->addMinute()) {
            throw new JsonException('timestamp is invalid', 4);
        }



        return $next($request);
    }
}
