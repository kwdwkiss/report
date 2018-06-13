<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/5/29
 * Time: 下午3:17
 */

namespace App\Http\Controllers\Index;

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
        abort_if(!isset($pageList[$page]), 403);
        $page = $pageList[$page];
        $geo = $this->getGeo(get_client_ip());
        return view('check_tb.tb', compact('page', 'geo'));
    }

    public function pdd()
    {
        $page = request('page', 'personal');
        $pageList = [
            'personal' => 'http://mobile.yangkeduo.com/personal.html',
            'complaint_list' => 'http://mobile.yangkeduo.com/complaint_list/complaint_list.html',
        ];
        abort_if(!isset($pageList[$page]), 403);
        $page = $pageList[$page];
        $geo = $this->getGeo(get_client_ip());
        return view('check_tb.pdd', compact('page', 'geo'));
    }

    public function jd()
    {
        $pageKey = request('page');
        $pageList = [
            'jd_member' => 'https://vip.m.jd.com/?sceneval=2&sid=',
            'credit' => 'https://m.jr.jd.com/jdbt/credit/index.html',
            'my_jd' => 'https://home.m.jd.com/myJd/newhome.action',
            'account' => 'https://wqs.jd.com/my/accountv2.shtml?sceneval=2',
        ];
        abort_if(!isset($pageList[$pageKey]), 403);
        $page = $pageList[$pageKey];
        $geo = $this->getGeo(get_client_ip());
        if ($pageKey == 'jd_member') {
            return view('check_tb.jd_fix', compact('page', 'geo'));
        }
        return view('check_tb.jd', compact('page', 'geo'));
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
}