<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/21
 * Time: 上午10:08
 */

namespace App\Http\Controllers\Admin;

use App\AccountReport;
use App\AccountSearch;
use App\Attachment;
use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\RechargeBill;
use App\Statement;
use App\Taxonomy;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\File;

class IndexController extends Controller
{
    public function index()
    {
        $config = [
            'taxonomy' => Taxonomy::allData(),
            'user' => \Auth::guard('admin')->user(),
        ];
        return view('admin', compact('config'));
    }

    public function info()
    {
        return ['data' => \Auth::guard('admin')->user()];
    }

    public function login()
    {
        if (\Auth::guard('admin')->attempt([
            'name' => request('username'),
            'password' => request('password')
        ], request('remember'))) {
            return [];
        }
        return [
            'code' => 100,
            'message' => '登录失败'
        ];
    }

    public function logout()
    {
        \Auth::guard('admin')->logout();
        return [];
    }

    public function modifyPassword()
    {
        $this->validate(request(), [
            'newPassword' => 'required|min:8'
        ]);
        $user = \Auth::guard('admin')->user();
        if (!\Hash::check(request('password'), $user->password)) {
            return [
                'code' => -1,
                'message' => '密码错误'
            ];
        }
        $user->update([
            'password' => bcrypt(request('newPassword'))
        ]);
        return [];
    }

    public function upload()
    {
        $uploadFile = request()->file('file');

        if (!$uploadFile) {
            throw new JsonException('上传文件失败，请稍后再次尝试');
        }
        if (!$uploadFile->isValid()) {
            throw new JsonException('上传文件失败，请稍后再次尝试');
        }

        $user = \Auth::guard('admin')->user();

        return Attachment::createForLocal($uploadFile, $user);
    }

    public function uploadOss()
    {
        $user = \Auth::guard('admin')->user();
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

        $limit = 1000;

        if ($size / 1024 > $limit) {
            return [
                'code' => -1,
                'message' => "上传文件不能超过{$limit}KB"
            ];
        }

        $user = \Auth::guard('admin')->user();

        return Attachment::createForOss($uploadFile, $user, env('ALIYUN_OSS_BUCKET_ADMIN'));
    }

    public function uploadOssImage()
    {
        $user = \Auth::guard('admin')->user();

        if (!$user) {
            throw new JsonException('用户未登录，请登录后再上传图片');
        }

        $uploadFile = request()->file('file');

        if (!$uploadFile) {
            throw new JsonException('上传文件失败，请稍后再次尝试');
        }
        if (!$uploadFile->isValid()) {
            throw new JsonException('上传文件失败，文件大小必须小于5M，请稍后再次尝试');
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

    public function statement()
    {
        $user = \Auth::guard('admin')->user();
        if ($user->id != 1) {
            throw new JsonException('无权访问');
        }

        $registerTotal = \Cache::remember('statement.user.register.total', 10, function () {
            return User::query()->count();
        });

        $registerToday = \Cache::remember('statement.user.register.today', 10, function () {
            return User::query()
                ->whereDate('created_at', Carbon::today()->toDateString())
                ->count();
        });

        $registerTodayInviter = \Cache::remember('statement.user.register.today_inviter', 10, function () {
            return User::query()
                ->whereHas('_profile', function ($query) {
                    $query->where('inviter', '!=', '');
                })
                ->whereDate('created_at', Carbon::today()->toDateString())
                ->count();
        });

        $registerMonth = \Cache::remember('statement.user.register.month', 10, function () {
            return User::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();
        });

        $registerLastMonth = \Cache::remember('statement.user.register.last_month', 3600 * 24, function () {
            return User::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->subMonths(1)->month)
                ->count();
        });

        $row = Statement::query()
            ->where('date', Carbon::yesterday()->toDateString())
            ->first();
        $registerYesterday = $row ? $row->user_register : 0;
        $registerYesterdayInviter = $row ? $row->user_register_inviter : 0;
        $reportYesterday = $row ? $row->account_report : 0;
        $searchYesterday = $row ? $row->account_search : 0;
        $rechargeYesterday = $row ? $row->recharge_money : 0;
        $rechargeYesterdayOnce = $row ? $row->recharge_first_user : 0;

        $reportToday = \Cache::remember('statement.user.report.total', 10, function () {
            return AccountReport::query()
                ->whereDate('created_at', Carbon::today()->toDateString())
                ->count();
        });

        $reportMonth = \Cache::remember('statement.user.report.month', 10, function () {
            return AccountReport::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();
        });

        $reportLastMonth = \Cache::remember('statement.user.report.last_month', 3600 * 24, function () {
            return AccountReport::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->subMonths(1)->month)
                ->count();
        });

        $searchToday = \Cache::remember('statement.user.search.total', 10, function () {
            return AccountSearch::query()
                ->whereDate('created_at', Carbon::today()->toDateString())
                ->count();
        });

        $searchMonth = \Cache::remember('statement.user.search.month', 10, function () {
            return AccountSearch::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();
        });

        $searchLastMonth = \Cache::remember('statement.user.search.last_month', 3600 * 24, function () {
            return AccountSearch::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->subMonths(1)->month)
                ->count();
        });

        $rechargeToday = \Cache::remember('statement.user.recharge.total', 10, function () {
            return RechargeBill::query()
                ->whereDate('created_at', Carbon::today()->toDateString())
                ->count();
        });

        $rechargeMonth = \Cache::remember('statement.user.recharge.month', 10, function () {
            return RechargeBill::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();
        });

        $rechargeLastMonth = \Cache::remember('statement.user.recharge.last_month', 3600 * 24, function () {
            return RechargeBill::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->subMonths(1)->month)
                ->count();
        });

        $rechargeTodayOnce = \Cache::remember('statement.user.recharge.today_once', 10, function () {
            $todayUserIds = RechargeBill::query()
                ->select('user_id')
                ->whereDate('created_at', Carbon::today()->toDateString())
                ->get()->pluck('user_id')->toArray();
            $subQuery = RechargeBill::query()
                ->whereIn('user_id', $todayUserIds)
                ->groupBy('user_id')
                ->havingRaw('count(*)=1');
            return \DB::table(\DB::raw("({$subQuery->toSql()}) as a"))->mergeBindings($subQuery->getQuery())->count();
        });

        return [
            'data' => [
                'userRegister' => [
                    'total' => $registerTotal,
                    'today' => $registerToday,
                    'yesterday' => $registerYesterday,
                    'month' => $registerMonth,
                    'lastMonth' => $registerLastMonth,
                    'todayInviter' => $registerTodayInviter,
                    'yesterdayInviter' => $registerYesterdayInviter
                ],
                'accountReport' => [
                    'today' => $reportToday,
                    'yesterday' => $reportYesterday,
                    'month' => $reportMonth,
                    'lastMonth' => $reportLastMonth
                ],
                'accountSearch' => [
                    'today' => $searchToday,
                    'yesterday' => $searchYesterday,
                    'month' => $searchMonth,
                    'lastMonth' => $searchLastMonth
                ],
                'rechargeBill' => [
                    'today' => $rechargeToday,
                    'yesterday' => $rechargeYesterday,
                    'month' => $rechargeMonth,
                    'lastMonth' => $rechargeLastMonth,
                    'todayOnce' => $rechargeTodayOnce,
                    'yesterdayOnce' => $rechargeYesterdayOnce
                ]
            ]
        ];
    }
}