<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/12/1
 * Time: 下午2:27
 */

namespace App\Http\Controllers\Index;

use App\Account;
use App\AccountReport;
use App\Config;
use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccountReportResource;
use App\Http\Resources\AccountResource;
use App\Taxonomy;

class IndexController extends Controller
{
    public function index()
    {
        $page = [];
        \DB::transaction(function () use (&$page, &$taxonomy) {
            $page = array_merge([
                'taxonomy' => Taxonomy::allDisplay(),
            ], Config::getSiteIndex(), Config::getSiteStatics());
        });
        return view('index', compact('page'));
    }

    public function popWindow()
    {
        return ['data' => Config::getSitePopWindow()];
    }

    public function search()
    {
        request()->query->set('ip_hide', 1);
        $account_type = request('account_type');
        $name = request('name');

        Taxonomy::where('pid', Taxonomy::ACCOUNT_TYPE)->findOrFail($account_type);
        if ($account_type == 201 && !preg_match('/^[1-9][0-9]{4,14}$/', $name)) {
            throw new JsonException('QQ号码格式错误');
        }
        if ($account_type == 204 && !preg_match('/^1(3[0-9]|4[579]|5[0-35-9]|7[0-9]|8[0-9])\d{8}$/', $name)) {
            throw new JsonException('手机号码格式错误');
        }

        $account = Account::where('type', $account_type)->where('name', $name)->first();

        //type 1-显示无记录 2-显示记录列表 3-显示账号信息 4-显示骗子
        $query = AccountReport::query()
            ->where('account_type', $account_type)
            ->where('account_name', $name)
            ->where('display', 1)
            ->orderBy('created_at', 'desc');
        if (!$account) {
            $accountReports = $query->get();
            if (count($accountReports) > 0) {
                return ['data' => [
                    'type' => 2,
                    'account_reports' => AccountReportResource::collection($accountReports)
                ]];
            } else {
                return ['data' => ['type' => 1, 'name' => $name]];
            }
        } else {
            switch ($account->status) {
                case 101:
                    return ['data' => ['type' => 1]];
                    break;
                case 102:
                    $accountReports = $query->get();
                    if (count($accountReports) > 0) {
                        return ['data' => [
                            'type' => 2,
                            'account_reports' => AccountReportResource::collection($accountReports)
                        ]];
                    } else {
                        return ['data' => ['type' => 1]];
                    }
                    break;
                case 103:
                case 105:
                case 106:
                    return ['data' => ['type' => 3, 'account' => new AccountResource($account)]];
                    break;
                case 104:
                    return ['data' => ['type' => 4, 'account' => new AccountResource($account)]];
                    break;
            }
        }
    }

    public function report()
    {
        $account_type = request('account_type');
        $name = trim(request('name'));
        $report_type = request('report_type');
        $captcha = request('captcha');

        if (!captcha_check($captcha)) {
            throw new JsonException('验证码错误');
        }
        if ($account_type == 201 && !preg_match('/^[1-9][0-9]{4,14}$/', $name)) {
            throw new JsonException('QQ号码格式错误');
        }
        if ($account_type == 203 && !preg_match('/^[a-zA-Z]{1}[-_a-zA-Z0-9]{5,19}+$/', $name)) {
            throw new JsonException('微信号码格式错误');
        }
        if ($account_type == 204 && !preg_match('/^1(3[0-9]|4[579]|5[0-35-9]|7[0-9]|8[0-9])\d{8}$/', $name)) {
            throw new JsonException('手机号码格式错误');
        }
        if (is_null($name)) {
            throw new JsonException('账号不能为空');
        }

        $todayDate = date('Y-m-d', time());
        $todayReport = AccountReport::where('account_type', $account_type)
            ->where('account_name', $name)
            ->where('created_at', '>', $todayDate)
            ->first();
        if ($todayReport) {
            throw new JsonException('每天对同一账号类型只能举报一次');
        }

        AccountReport::report($account_type, $name, $report_type, request()->getClientIp());

        return [];
    }
}