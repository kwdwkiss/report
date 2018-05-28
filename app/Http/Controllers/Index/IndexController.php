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
use App\BehaviorLog;
use App\Config;
use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccountReportResource;
use App\Http\Resources\AccountResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\UserResource;
use App\Taxonomy;
use Carbon\Carbon;
use Detection\MobileDetect;

class IndexController extends Controller
{
    public function index()
    {
//        $detect = new MobileDetect();
//        if ($detect->isMobile()) {
//            return redirect('/mobile');
//        }
        $page = [];
        \DB::transaction(function () use (&$page) {
            $user = \Auth::guard('user')->user();
            if ($user) {
                $user = new UserResource($user);
                $unreadNotification = $user->unreadNotifications()->count();
            } else {
                $user = null;
                $unreadNotification = 0;
            }
            $page = array_merge([
                'user' => $user,
                'unreadNotification' => $unreadNotification,

                'index_blog_article' => Config::get('site.index_blog_article'),
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
        if (!$user) {
            throw new JsonException('用户未登录，请登录后再查询');
        }
        if (!$user->_profile) {
            throw new JsonException('用户数据异常，请联系客服');
        }

        request()->query->set('ip_hide', 1);
        $name = request('name');

        if (!$name) {
            throw new JsonException('查询账号为空');
        }

        if ($user->_profile->amount < 2) {
            throw new JsonException('用户积分不足，请充值');
        }
        $user->_profile->decrement('amount', 2);

        AccountSearch::create([
            'user_id' => $user ? $user->id : 0,
            'type' => 0,
            'name' => $name,
            'ip' => request()->getClientIp(),
            'success' => 1,
            'result' => ''
        ]);

        $accounts = Account::where('name', $name)->get();

        //type 1-显示无记录 2-显示记录列表 3-显示账号信息 4-显示骗子
        $accountReports = AccountReport::query()
            ->where('account_name', $name)
            ->where('display', 1)
            ->orderBy('created_at', 'desc')->get();

        return [
            'data' => [
                'name' => $name,
                'accounts' => AccountResource::collection($accounts),
                'account_reports' => AccountReportResource::collection($accountReports)
            ]
        ];
    }

    public function report()
    {
        $user = \Auth::guard('user')->user();
        if (!$user) {
            throw new JsonException('用户未登录，请登录后再举报');
        }

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
        if (!$name) {
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
        $accountType = Taxonomy::where('pid', Taxonomy::ACCOUNT_TYPE)->findOrFail($account_type);
        $reportType = Taxonomy::where('pid', Taxonomy::REPORT_TYPE)->findOrFail($report_type);

        //检查举报字段合法性
        $imageLimit = array_get(AccountReport::$imageLimit, $account_type, []);
        if (in_array($report_type, $imageLimit) && !$attachment) {
            throw new JsonException("为了提高举报数据真实性，{$accountType->name}{$reportType->name}，需要上传图片证据。");
        }

        //同一账号每天限制举报
        $todayReportCount = AccountReport::query()
            ->where('user_id', $user->id)
            ->where('account_type', $account_type)
            ->where('account_name', $name)
            ->whereDate('created_at', Carbon::today()->toDateString())
            ->count();
        if ($todayReportCount > 0) {
            throw new JsonException('一个用户每天对同一账号只能举报一次');
        }

        \DB::transaction(function () use ($user, $account_type, $name, $report_type, $ip, $description, $attachment
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
                'user_id' => $user ? $user->id : 0,
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
        $user = \Auth::guard('user')->user();
        if (!$user) {
            throw new JsonException('用户未登录，请登录后再上传图片');
        }

        $uploadFile = request()->file('file');

        if (!$uploadFile) {
            throw new JsonException('上传文件失败，请稍后再次尝试');
        }
        if (!$uploadFile->isValid()) {
            throw new JsonException('上传文件失败，请稍后再次尝试');
        }

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

    public function behaviorLog()
    {
        $type = request('type');
        $content = request('content', '');

        $user = \Auth::guard('user')->user();
        $userId = $user ? $user->id : 0;

        if (!isset(BehaviorLog::$types[$type])) {
            throw new JsonException('类型错误');
        }

        BehaviorLog::create([
            'user_id' => $userId,
            'type' => $type,
            'content' => $content
        ]);

    }
}