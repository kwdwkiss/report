<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/12/1
 * Time: 下午2:27
 */

namespace Modules\Index\Http\Controllers;

use Carbon\Carbon;
use Cly\RegExp\RegExp;
use Cly\Spreadsheet\NoScienceValueBinder;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use Modules\Common\Entities\Account;
use Modules\Common\Entities\AccountFavor;
use Modules\Common\Entities\AccountReport;
use Modules\Common\Entities\AccountSearch;
use Modules\Common\Entities\AmountBill;
use Modules\Common\Entities\Attachment;
use Modules\Common\Entities\BehaviorLog;
use Modules\Common\Entities\Config;
use Modules\Common\Entities\Taxonomy;
use Modules\Common\Entities\User;
use Modules\Common\Entities\UserProduct;
use Modules\Common\Exceptions\JsonException;
use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\AccountFavorResource;
use Modules\Common\Transformers\AccountReportResource;
use Modules\Common\Transformers\UserResource;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class IndexController extends Controller
{
    public function index()
    {
//        $detect = new MobileDetect();
//        if ($detect->isMobile()) {
//            return redirect('/mobile');
//        }

//        $routes = app('router')->getRoutes();
//        $routes = collect($routes)->map(function (Route $route) {
//            return [
//                'host' => $route->domain(),
//                'method' => implode('|', $route->methods()),
//                'uri' => $route->uri(),
//                'name' => $route->getName(),
//                'action' => $route->getActionName(),
//                'middleware' => $this->getMiddleware($route),
//            ];
//        });

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
            $page = array_merge($page, [
                'user' => $user,
                'unreadNotification' => $unreadNotification,

                'index_blog_article' => Config::get('site.index_blog_article'),
                'basic' => Config::getBasic(),
                'taxonomy' => Taxonomy::allDisplay(),
            ], Config::getSiteIndex(), Config::getSiteStatics());
        });
        return view('index::index', compact('page', 'routes'));
    }

    public function basic()
    {
        return ['data' => Config::getBasic()];
    }

    public function popWindow()
    {
        return ['data' => Config::getSitePopWindow()];
    }

    public function search()
    {
        $user = \Auth::guard('user')->user();
//        if (!$user) {
//            throw new JsonException('用户未登录，请登录后再查询');
//        }
//        if (!$user->_profile) {
//            throw new JsonException('用户数据异常，请联系客服');
//        }

        $name = request('name');
        if (!$name) {
            throw new JsonException('查询账号为空');
        }
//        if ($user->_profile->amount < 2) {
//            throw new JsonException('用户积分不足，请充值');
//        }

        $data = [];
        \DB::transaction(function () use ($user, $name, &$data) {

//            $user->_profile->decrement('amount', 2);

            AccountSearch::create([
                'user_id' => $user ? $user->id : 0,
                'type' => 0,
                'name' => $name,
                'ip' => get_client_ip(),
                'success' => 1,
                'result' => ''
            ]);

//            $accountFavorsTotal = AccountFavor::query()
//                ->select(\DB::raw('*,count(*) as total'))
//                ->where('account_name', $name)
//                ->groupBy('account_name', 'account_type')
//                ->get();
//            $accountFavors = AccountFavor::query()
//                ->where('account_name', $name)
//                ->orderBy('id', 'desc')
//                ->limit(5)
//                ->get();

            //存在账号的用户
            $searchUsers = User::query()->with('_profile')
                ->where('mobile', $name)
                ->orWhere('qq', $name)
                ->orWhere('ww', $name)
                ->orWhere('wx', $name)
                ->orWhere('jd', $name)
                ->orWhere('is', $name)
                ->get();

            //type 1-显示无记录 2-显示记录列表 3-显示账号信息 4-显示骗子
            $query = AccountReport::query()->with('_attachments');

            $accountReports = $query
                ->where('account_name', $name)
                ->where('display', 1)
                ->orderBy('created_at', 'desc')->get();

            $userCheckList = [];
            foreach ($searchUsers as $item) {
                if ($item->isAuth()) {
                    if ($item->mobile == $name) {
                        $userCheckList[204] = 1;
                        continue;
                    }
                    if ($item->qq == $name) {
                        $userCheckList[201] = 1;
                        continue;
                    }
                    if ($item->ww == $name) {
                        $userCheckList[202] = 1;
                        continue;
                    }
                    if ($item->wx == $name) {
                        $userCheckList[203] = 1;
                        continue;
                    }
                    if ($item->jd == $name) {
                        $userCheckList[205] = 1;
                        continue;
                    }
                    if ($item->is == $name) {
                        $userCheckList[207] = 1;
                        continue;
                    }
                }
            }

            $accountReportsData = [];
            foreach ($accountReports as $item) {
                if (isset($userCheckList[$item->account_type])) {
                    if (empty($item->description) || $item->_attachments->isEmpty()) {
                        continue;
                    }
                }
                $accountReportsData[] = $item;
            }
            $accountReports = new Collection($accountReportsData);

            request()->query->set('ip_hide', 1);
            request()->query->set('r_index', true);
            $data = [
                'name' => $name,
                'user' => UserResource::collection($searchUsers),
                'account_reports' => AccountReportResource::collection($accountReports),
                'account_favors' => [],//AccountFavorResource::collection($accountFavors),
                'account_favors_total' => [],//AccountFavorResource::collection($accountFavorsTotal),
            ];
        });

        return ['data' => $data];
    }

    public function rechargeUrl()
    {
        $user = \Auth::guard('user')->user();
        if (!$user) {
            throw new JsonException('用户未登录，请登录后再充值');
        }

        $appid = '2018062932';
        $domain = 'pay.tbpzw.com';
        $host = request()->getHttpHost();
        $back_url = base64_encode("$host/user/recharge/page-callback");
        $rechargeUrl = "http://{$domain}/pay/?appid={$appid}&payno={$user->mobile}&back_url={$back_url}";
        return [
            'data' => [
                'recharge_url' => $rechargeUrl,
                'mobile' => $user->mobile,
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
//        $captcha = request('captcha');
        $description = request('description');
        $attachmentData = request('image');
        $attachmentData1 = request('image1');
        $attachmentData2 = request('image2');
        $ip = get_client_ip();
        $attachment = Attachment::find(array_get($attachmentData, 'id'));
        $attachment1 = Attachment::find(array_get($attachmentData1, 'id'));
        $attachment2 = Attachment::find(array_get($attachmentData2, 'id'));

//        if (!captcha_check($captcha)) {
//            throw new JsonException('验证码错误');
//        }
        if (!$name) {
            throw new JsonException('账号不能为空');
        }
        if ($account_type == 201 && !preg_match(RegExp::QQ, $name)) {
            throw new JsonException('QQ号码格式错误');
        }
        if ($account_type == 203 && !preg_match(RegExp::WX, $name)) {
            throw new JsonException('微信号码格式错误');
        }
        if ($account_type == 204 && !preg_match(RegExp::MOBILE, $name)) {
            throw new JsonException('手机号码格式错误');
        }
        $accountType = Taxonomy::where('pid', Taxonomy::ACCOUNT_TYPE)->findOrFail($account_type);
        $reportType = Taxonomy::where('pid', Taxonomy::REPORT_TYPE)->findOrFail($report_type);

        //检查举报字段合法性
//        $imageLimit = array_get(AccountReport::$imageLimit, $account_type, []);
//        if (in_array($report_type, $imageLimit) && !$attachment) {
//            throw new JsonException("为了提高举报数据真实性，{$accountType->name}{$reportType->name}，需要上传图片证据。");
//        }

        //同一账号每天限制举报
//        $todayReportCount = AccountReport::query()
//            ->where('user_id', $user->id)
//            ->where('account_type', $account_type)
//            ->where('account_name', $name)
//            ->whereDate('created_at', Carbon::today()->toDateString())
//            ->count();
//        if ($todayReportCount > 0) {
//            throw new JsonException('一个用户每天对同一账号只能举报一次');
//        }

        //关联用户是审核过了必须证据举报
//        $query = User::query();
//        switch ($account_type) {
//            case 201:
//                $query->where('qq', $name);
//                break;
//            case 202:
//                $query->where('ww', $name);
//                break;
//            case 203:
//                $query->where('wx', $name);
//                break;
//            case 204:
//                $query->where('mobile', $name);
//                break;
//            case 205:
//                $query->where('jd', $name);
//                break;
//            case 207:
//                $query->where('is', $name);
//                break;
//        }
//        $reportUser = $query->first();
        //举报会员需要联系客服
//        if ($reportUser && $reportUser->isAuth()) {
//            throw new JsonException('此用户为宏海实名认证会员，请联系客服举报');
//        }
//        if ($reportUser && $reportUser->isAuth() && (empty($description) || empty($attachment))) {
//            throw new JsonException('举报的账号为宏海认证会员，必须同时提交图片和描述才能举报');
//        }

        $account = Account::where('type', $account_type)->where('name', $name)->first();
        if (!$account) {
            $account = Account::create([
                'type' => $account_type,
                'name' => $name,
                'status' => 102
            ]);
        }

        $userReport = $user->_report;
        if (!$userReport) {
            throw new JsonException('用户未开通举报功能');
        }

        \DB::transaction(function () use (
            $account, $user, $account_type, $name,
            $report_type, $ip, $description, $attachment, $attachment1, $attachment2
        ) {
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
                $attachment->update(['use' => 1]);
                $accountReport->_attachments()->attach($attachment);
            }
            if ($attachment1) {
                $attachment1->update(['use' => 1]);
                $accountReport->_attachments()->attach($attachment1);
            }
            if ($attachment2) {
                $attachment2->update(['use' => 1]);
                $accountReport->_attachments()->attach($attachment2);
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

        $uploadFile = request('file');
        if (!$uploadFile) {
            throw new JsonException('上传文件失败，请稍后再次尝试');
        }

        if ($uploadFile instanceof UploadedFile) {
            $uploadFile = request()->file('file');
            if (!$uploadFile->isValid()) {
                $errorMessage = $uploadFile->getErrorMessage();
                throw new JsonException("上传文件失败：${errorMessage}");
            }
        } elseif (is_string($uploadFile)) {
            if (!preg_match('/^(data:\s*image\/(\w+);base64,)/', $uploadFile, $result)) {
                throw new JsonException('上传base64图片格式错误');
            }
            //$imageType = $result[2]; //data:image/jpeg;base64,
            //$output_file = $output_directory . '/' . md5(time()) . '.' . $imageType;
            $filename = storage_path() . '/' . md5(microtime());

            $fileBinary = base64_decode(str_replace($result[1], '', $uploadFile));
            file_put_contents($filename, $fileBinary);

            $uploadFile = new File($filename);
        } else {
            throw  new JsonException('上传文件格式错误');
        }

        $watermark = request('watermark', '');
        $watermarkList = [
            'identity' => public_path('images/indentity_watermark.png'),
        ];
        $watermark = array_get($watermarkList, $watermark);

        $size = $uploadFile->getSize();//byte

        $limit = 200;

        if ($size / 1024 > $limit || $watermark) {
            try {
                $image = \Image::make($uploadFile);
            } catch (\Exception $e) {
                throw new JsonException('图片只支持JPG，PNG，GIF格式。');
            }
            if ($image->width() > 600) {
                $image->widen(600);
            }
            if ($watermark && is_file($watermark)) {
                $image->insert($watermark, 'center');
            }
            $filename = storage_path() . '/' . md5(microtime());
            $image->save($filename);
            $uploadFile = new File($filename);
        }

        $result = Attachment::createForOss($uploadFile, $user);

        unlink($uploadFile->getRealPath());

        return $result;
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

        return [];
    }

    public function excelCostType()
    {
        $user = \Auth::guard('user')->user();

        if (!$user) {
            return ['data' => '未登录'];
        }

        $hasEnableExcel = UserProduct::hasEnableProducts($user, 'excel');

        $type = '按次扣费';
        if ($hasEnableExcel) {
            $type = 'EXCEL包月';
        }

        return ['data' => $type];
    }

    public function oneKeyExcel()
    {
        $data = request('data', []);
        $user = \Auth::guard('user')->user();

        if (!$user) {
            throw new JsonException('请登录后再下载表格');
        }
        if (!is_array($data)) {
            throw new JsonException('数据格式错误');
        }
        if (empty($data)) {
            throw new JsonException('数据为空');
        }

        $limit = 2;
        $redis = app('redis');
        $key = 'user.excel.download:' . $user->id;
        $result = $redis->setnx($key, microtime(true));
        if (!$result) {
            logger('user.excel.download.limit', ['user_id' => $user->id, 'time' => microtime(true)]);
            throw new JsonException('请求过于频繁');
        }
        $redis->expire($key, $limit);

        $filename = 'temp/' . date('YmdHis', time()) . str_random(4) . '.xlsx';
        $path = \Storage::disk('public')->path($filename);

        //记录日志
        BehaviorLog::create([
            'user_id' => $user ? $user->id : 0,
            'type' => 2,
            'content' => json_encode($data, JSON_UNESCAPED_UNICODE)
        ]);

        //给=前面加'
        foreach ($data as &$row) {
            foreach ($row as &$col) {
                if (strpos($col, '=') === 0) {
                    $col = "'" . $col;
                }
            }
        }

        \DB::transaction(function () use ($user, $data, $path) {
            $amount = 10;
            $hasEnableExcel = UserProduct::hasEnableProducts($user, 'excel');
            if (!$hasEnableExcel && $user->_profile->amount < 10) {
                throw new JsonException('用户积分不足，请充值');
            }

            //下载
            try {
                Cell::setValueBinder(new NoScienceValueBinder());
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->fromArray($data);
                $writer = new Xlsx($spreadsheet);
                $writer->save($path);
            } catch (Exception $e) {
                logger($e->getMessage(), $data);
                throw $e;
            }

            if (!$hasEnableExcel) {
                //扣积分
                AmountBill::create([
                    'user_id' => $user->id,
                    'bill_no' => AmountBill::generateBillNo($user->id),
                    'biz_id' => 6,
                    'biz_type' => 103,
                    'type' => 1,
                    'amount' => $amount,
                    'description' => "下载EXCEL，扣除{$amount}积分",
                    'created_at' => Carbon::now()->toDateTimeString(),
                ]);
                $user->_profile->decrement('amount', $amount);

                //推荐好友下载EXCEL，提成4积分
                $inviter = $user->_profile->_inviter;
                if ($inviter) {
                    $inviterAmount = 4;
                    $inviter->_profile->increment('amount', $inviterAmount);
                    AmountBill::create([
                        'user_id' => $inviter->id,
                        'bill_no' => AmountBill::generateBillNo($inviter->id),
                        'type' => 0,
                        'amount' => $inviterAmount,
                        'user_amount' => $inviter->_profile->amount,
                        'biz_type' => 3,
                        'biz_id' => 7,
                        'description' => "推荐好友下载EXCEL,提成{$inviterAmount}积分",
                    ]);
                }
            }
        });

//        $fp = fopen($path, 'w');
//        fputs($fp, chr(239) . chr(187) . chr(191));
//        foreach ($data as $row) {
//            if (!is_array($row)) {
//                continue;
//            }
//            fputcsv($fp, $row);
//        }
//        fclose($fp);

        return ['data' => asset('storage/' . $filename)];
    }
}