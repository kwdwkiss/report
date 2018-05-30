<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/5/29
 * Time: 下午3:17
 */

namespace App\Http\Controllers\Index;


class CheckTbController
{
    public function index()
    {
        $page = request('page', 'my_rate');
        $pageList = [
            'my_rate' => 'https://rate.taobao.com/user-myrate-UvCHyMmg0MFxT--buyerOrSeller%7C3--receivedOrPosted%7C1.htm',
            'item_list' => 'https://buyertrade.taobao.com/trade/itemlist/list_bought_items.htm',
            'raise_naughty' => 'https://market.m.taobao.com/apps/market/m-vip/raise-naughty.html',
            'appeal_center' => 'https://passport.taobao.com/ac/h5/appeal_center.htm?fromSite=0'
        ];
        $page = $pageList[$page];
        $ip = request()->getClientIp();
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
        return view('check_tb.index', compact('page', 'geo'));
    }
}