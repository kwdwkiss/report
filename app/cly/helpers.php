<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/6/9
 * Time: 下午10:19
 */
if (!function_exists('get_client_ip')) {
    function get_client_ip()
    {
        //判断服务器是否允许$_SERVER
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            //不允许就使用getenv获取
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            } elseif (getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            } else {
                $realip = getenv("REMOTE_ADDR");
            }
        }

        return $realip;
    }
}

if (!function_exists('get_geo')) {
    function get_geo($ip)
    {
        $result = @file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip);
        if ($result) {
            $result = json_decode($result, true);
            return array_get($result, 'data');
        }
    }
}

if (!function_exists('get_geo_str')) {
    function get_geo_str($ip)
    {
        $data = get_geo($ip);
        return array_get($data, 'region') . ' ' . array_get($data, 'city');
    }
}

if (!function_exists('debug_backtrace_print')) {
    function debug_backtrace_print()
    {
        foreach (debug_backtrace() as $item) {
            echo array_get($item, 'file') . '(' . array_get($item, 'line') . ')' . PHP_EOL;
        }
    }
}
