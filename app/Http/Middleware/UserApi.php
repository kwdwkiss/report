<?php

namespace App\Http\Middleware;

use App\Exceptions\JsonException;
use App\User;
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
        $time = Carbon::createFromTimestamp($timestamp);
        if ($time < Carbon::now()->subMinute() || $time > Carbon::now()->addMinute()) {
            throw new JsonException('timestamp is invalid', 4);
        }
        $user = User::where('api_key', $api_key)->first();
        if (!$user) {
            throw new JsonException('api_key error', 5);
        }
        $api_secret = $user->api_secret;
        if (!$api_secret) {
            throw new JsonException('api_secret null', 6);
        }

        unset($input['sign']);
        ksort($input);
        $str = http_build_query($input);
        if ($sign != md5($str . $api_secret)) {
            throw new JsonException('sign invalid', 7);
        }
        \Auth::guard('user')->setUser($user);

        return $next($request);
    }
}
