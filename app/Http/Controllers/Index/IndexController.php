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
use App\AccountSearch;
use App\Attachment;
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
        $user = \Auth::guard('user')->user();

        request()->query->set('ip_hide', 1);
        $account_type = request('account_type');
        $name = request('name');

        if (!$user) {
            throw new JsonException('用户未登录，请登录后再查询');
        }
        if (!$name) {
            throw new JsonException('查询账号为空');
        }

        try {
            Taxonomy::where('pid', Taxonomy::ACCOUNT_TYPE)->findOrFail($account_type);
            if ($account_type == 201 && !preg_match('/^[1-9][0-9]{4,14}$/', $name)) {
                throw new JsonException('QQ号码格式错误');
            }
            if ($account_type == 203 && !preg_match('/^[a-zA-Z]{1}[-_a-zA-Z0-9]{5,19}+$/', $name)) {
                throw new JsonException('微信号码格式错误');
            }
            if ($account_type == 204 && !preg_match('/^1(3[0-9]|4[579]|5[0-35-9]|7[0-9]|8[0-9])\d{8}$/', $name)) {
                throw new JsonException('手机号码格式错误');
            }
        } catch (JsonException $e) {
            AccountSearch::create([
                'user_id' => $user ? $user->id : 0,
                'type' => $account_type,
                'name' => $name,
                'ip' => request()->getClientIp(),
                'success' => 0,
                'result' => $e->getMessage()
            ]);
            throw $e;
        }
        AccountSearch::create([
            'user_id' => $user ? $user->id : 0,
            'type' => $account_type,
            'name' => $name,
            'ip' => request()->getClientIp(),
            'success' => 1,
            'result' => ''
        ]);

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
        $description = request('description');
        $attachmentData = request('image');
        $ip = request()->getClientIp();
        $attachment = Attachment::find(array_get($attachmentData, 'attachment.id'));

        if (!captcha_check($captcha)) {
            throw new JsonException('验证码错误');
        }
        if (is_null($name)) {
            throw new JsonException('账号不能为空');
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
        Taxonomy::where('pid', Taxonomy::ACCOUNT_TYPE)->findOrFail($account_type);
        Taxonomy::where('pid', Taxonomy::REPORT_TYPE)->findOrFail($report_type);

        //同一账号每天限制举报
        $todayDate = date('Y-m-d', time());
        $todayReport = AccountReport::where('account_type', $account_type)
            ->where('account_name', $name)
            ->where('created_at', '>', $todayDate)
            ->first();
        if ($todayReport) {
            throw new JsonException('每天对同一账号类型只能举报一次');
        }

        \DB::transaction(function () use ($account_type, $name, $report_type, $ip, $description, $attachment
        ) {
            $account = Account::where('type', $account_type)->where('name', $name)->first();
            if (!$account) {
                $account = Account::create([
                    'type' => $account_type,
                    'name' => $name,
                    'status' => 102
                ]);
            }
            $account->increment('report_count');

            $accountReport = AccountReport::create([
                'account_type' => $account_type,
                'account_name' => $name,
                'type' => $report_type,
                'ip' => $ip,
                'description' => $description
            ]);

            //添加附件
            if ($attachment) {
                $accountReport->_attachments()->attach($attachment);
            }
        });

        return [];
    }

    public function uploadOss()
    {
        $uploadFile = request()->file('file');

        $size = $uploadFile->getSize();//byte

        $limit = 500;

        if ($size / 1024 > $limit) {
            return [
                'code' => -1,
                'message' => "上传文件不能超过{$limit}KB"
            ];
        }

        $user = \Auth::guard('admin')->user();

        return Attachment::createForOss($uploadFile, $user);
    }
}