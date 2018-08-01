<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/7/23
 * Time: 下午4:14
 */

namespace App\Http\Controllers\User;


use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\VbotJobResource;
use App\Jobs\VbotUserClear;
use App\VbotJob;
use Cly\Vbot\VbotService;

class VbotController extends Controller
{
    public function index()
    {

    }

    public function create()
    {
        $user = \Auth::guard('user')->user();

        $jobDoingCount = VbotJob::query()
            ->whereNotIn('status', [-1, -2])
            ->count();
        if ($jobDoingCount >= 50) {
            throw new JsonException('当前使用人数过多，请稍后再尝试');
        }

        $vbotJob = VbotJob::query()
            ->where('user_id', $user->id)
            ->whereNotIn('status', [-1, -2])
            ->first();

        if ($vbotJob) {
            throw new JsonException('任务正在进行中，请结束后再创建新任务');
        }

        VbotJob::create([
            'user_id' => $user->id,
            'status' => 0
        ]);

        //dispatch(new VbotUserClear($vbotJob));

        return [];
    }

    public function status()
    {
        $user = \Auth::guard('user')->user();

        $vbotJob = VbotJob::query()
            ->where('user_id', $user->id)
            ->whereNotIn('status', [-1, -2])
            ->first();

        return ['data' => $vbotJob ? new VbotJobResource($vbotJob) : ''];
    }
}