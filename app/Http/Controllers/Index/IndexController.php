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
use App\Http\Resources\UserResource;
use App\Taxonomy;
use App\User;
use Carbon\Carbon;
use Cly\RegExp\RegExp;
use Cly\Spreadsheet\NoScienceValueBinder;
use Illuminate\Http\File;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
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
                'taxonomy' => Taxonomy::allDisplay(),
            ], Config::getSiteIndex(), Config::getSiteStatics());
        });
        return view('index', compact('page', 'routes'));
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

        $name = request('name');
        if (!$name) {
            throw new JsonException('查询账号为空');
        }
        if ($user->_profile->amount < 2) {
            throw new JsonException('用户积分不足，请充值');
        }

        $searchUsers = null;
        $accountReports = null;
        \DB::transaction(function () use ($user, $name, &$searchUsers, &$accountReports) {
            $user->_profile->decrement('amount', 2);

            AccountSearch::create([
                'user_id' => $user ? $user->id : 0,
                'type' => 0,
                'name' => $name,
                'ip' => get_client_ip(),
                'success' => 1,
                'result' => ''
            ]);

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
        });

        request()->query->set('ip_hide', 1);
        request()->query->set('r_index', true);
        return [
            'data' => [
                'name' => $name,
                'user' => UserResource::collection($searchUsers),
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
        if ($account_type == 204 && !preg_match(RegExp::MOBILE, $name)) {
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

        //关联用户是审核过了必须证据举报
        $query = User::query();
        switch ($account_type) {
            case 201:
                $query->where('qq', $name);
                break;
            case 202:
                $query->where('ww', $name);
                break;
            case 203:
                $query->where('wx', $name);
                break;
            case 204:
                $query->where('mobile', $name);
                break;
            case 205:
                $query->where('jd', $name);
                break;
            case 207:
                $query->where('is', $name);
                break;
        }
        $reportUser = $query->first();
        if ($reportUser && $reportUser->isAuth() && (empty($description) || empty($attachment))) {
            throw new JsonException('举报的账号为宏海认证会员，必须同时提交图片和描述才能举报');
        }

        $account = Account::where('type', $account_type)->where('name', $name)->first();
        if (!$account) {
            $account = Account::create([
                'type' => $account_type,
                'name' => $name,
                'status' => 102
            ]);
        }

        \DB::transaction(function () use (
            $account, $user, $account_type, $name,
            $report_type, $ip, $description, $attachment
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
            $errorMessage = $uploadFile->getErrorMessage();
            throw new JsonException("上传文件失败：${errorMessage}");
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
            if ($image->height() > 600) {
                $image->heighten(600);
            } elseif ($image->width() > 600) {
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

    public function oneKeyExcel()
    {
        $data = request('data', []);

        if (!is_array($data)) {
            throw new JsonException('数据格式错误');
        }
        if (empty($data)) {
            throw new JsonException('数据为空');
        }

        $filename = 'temp/' . date('YmdHis', time()) . str_random(4) . '.xlsx';
        $path = \Storage::disk('public')->path($filename);

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
//        $fp = fopen($path, 'w');
//        fputs($fp, chr(239) . chr(187) . chr(191));
//        foreach ($data as $row) {
//            if (!is_array($row)) {
//                continue;
//            }
//            fputcsv($fp, $row);
//        }
//        fclose($fp);

        $user = \Auth::guard('user')->user();
        BehaviorLog::create([
            'user_id' => $user ? $user->id : 0,
            'type' => 2,
            'content' => json_encode($data, JSON_UNESCAPED_UNICODE)
        ]);

        return ['data' => asset('storage/' . $filename)];
    }
}