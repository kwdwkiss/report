<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/5/29
 * Time: 下午3:17
 */

namespace App\Http\Controllers\Index;


use Illuminate\Http\Response;

class CheckAccountController
{
    public function tb()
    {
        $page = request('page', 'my_rate');
        $pageList = [
            'my_rate' => 'https://rate.taobao.com/user-myrate-UvCHyMmg0MFxT--buyerOrSeller%7C3--receivedOrPosted%7C1.htm',
            'item_list' => 'https://buyertrade.taobao.com/trade/itemlist/list_bought_items.htm',
            'raise_naughty' => 'https://market.m.taobao.com/apps/market/m-vip/raise-naughty.html',
            'appeal_center' => 'https://passport.taobao.com/ac/h5/appeal_center.htm?fromSite=0',
            'account_profile' => 'https://member1.taobao.com/member/fresh/account_profile.htm',
        ];
        $page = $pageList[$page];
        $geo = $this->getGeo($this->getIp());
        return view('check_tb.tb', compact('page', 'geo'));
    }

    public function pdd()
    {
        $page = request('page', 'personal');
        $pageList = [
            'personal' => 'http://mobile.yangkeduo.com/personal.html',
            'complaint_list' => 'http://mobile.yangkeduo.com/complaint_list/complaint_list.html',
        ];
        $page = $pageList[$page];
        $geo = $this->getGeo($this->getIp());
        return view('check_tb.pdd', compact('page', 'geo'));
    }

    protected function getGeo($ip)
    {
        $result = @file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip);
        $ip = preg_replace('/(\d+\.\d+)\.\d+\.\d+/', '$1.*.*', $ip);
        if ($result) {
            $result = json_decode($result, true);
            $country = array_get($result, 'data.country');
            $region = array_get($result, 'data.region');
            $city = array_get($result, 'data.city');
            $isp = array_get($result, 'data.isp');
            $geo = "$ip $country $region $city $isp";
        } else {
            $geo = $ip;
        }
        return $geo;
    }

    protected function getIp()
    {
        //判断服务器是否允许$_SERVER
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
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

    public function ip()
    {
        $data = [];
        $ips = [$this->getIp()];
        foreach ($ips as $ip) {
            $geo = $this->getGeo($ip);
            $geo = explode(' ', $geo);
            array_shift($geo);
            $data[$ip] = implode(' ', $geo);
        }
        return view('check_tb.ip', compact('data'));
    }
}