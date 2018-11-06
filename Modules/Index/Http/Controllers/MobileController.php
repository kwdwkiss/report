<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/4/26
 * Time: 下午3:21
 */

namespace Modules\Index\Http\Controllers;


use Modules\Common\Entities\AccountReport;
use Modules\Common\Entities\AccountSearch;
use Modules\Common\Entities\Config;
use Modules\Common\Exceptions\JsonException;
use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\AccountReportResource;
use Modules\Common\Entities\Taxonomy;

class MobileController extends Controller
{
    public function index()
    {
        \DB::transaction(function () use (&$page) {
            $page = array_merge([
                'taxonomy' => Taxonomy::allDisplay(),
            ], Config::getSiteIndex(), Config::getSiteStatics());
        });
        return view('mobile', compact('page'));
    }

    public function search()
    {
        $user = \Auth::guard('user')->user();
        if (!$user) {
            throw new JsonException('用户未登录，请登录后再查询');
        }

        request()->query->set('ip_hide', 1);
        $name = request('name');

        if (!$name) {
            throw new JsonException('查询账号为空');
        }

        AccountSearch::create([
            'user_id' => $user ? $user->id : 0,
            'type' => 0,
            'name' => $name,
            'ip' => request()->getClientIp(),
            'success' => 1,
            'result' => ''
        ]);

        $query = AccountReport::query()
            ->where('account_name', $name)
            ->where('display', 1)
            ->orderBy('created_at', 'desc');

        $accountReports = $query->get();

        return AccountReportResource::collection($accountReports);
    }
}