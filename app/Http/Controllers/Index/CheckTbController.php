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
        return view('check_tb.index');
    }

    public function page()
    {
        $page = request('page', 'my_rate');
        $pageList = [
            'my_rate' => 'https://rate.taobao.com/user-myrate-UvCHyMmg0MFxT--buyerOrSeller%7C3--receivedOrPosted%7C1.htm',
            'item_list' => 'https://buyertrade.taobao.com/trade/itemlist/list_bought_items.htm',
            'raise_naughty' => 'https://market.m.taobao.com/apps/market/m-vip/raise-naughty.html?type=1',
            'appeal_center' => 'https://passport.taobao.com/ac/h5/appeal_center.htm?fromSite=0'
        ];
        $page = $pageList[$page];
        return view('check_tb.page', compact('page'));
    }
}