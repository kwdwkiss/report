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
use Cly\Vbot\VbotDeamon;
use Cly\Vbot\VbotManager;
use Cly\Vbot\VbotService;

class VbotController extends Controller
{
    public function index()
    {
        $user = \Auth::guard('user')->user();

        $query = VbotJob::query()
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->paginate();

        return VbotJobResource::collection($query);
    }

    public function create()
    {
        $name = request('name');

        $user = \Auth::guard('user')->user();

        if (!$name) {
            throw new JsonException('任务名称不能为空');
        }

        VbotJob::create([
            'user_id' => $user->id,
            'name' => $name,
            'status' => 0
        ]);
        return [];
    }

    public function detail()
    {
        $id = request('id');

        $user = \Auth::guard('user')->user();

        $vbotJob = VbotJob::query()->where('user_id', $user->id)->findOrFail($id);

        return new VbotJobResource($vbotJob);
    }

    public function delete()
    {
        $id = request('id');

        $user = \Auth::guard('user')->user();

        $vbotJob = VbotJob::query()->where('user_id', $user->id)->findOrFail($id);

        if ($vbotJob->status == 1) {
            throw new \Exception('任务运行中，请终止后再删除');
        }

        $vbotJob->delete();

        return [];
    }

    public function status()
    {
        $id = request('id');

        $user = \Auth::guard('user')->user();

        $vbotJob = VbotJob::query()
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();


        $manager = new VbotManager($vbotJob);
        return [
            'vbotJob' => new VbotJobResource($vbotJob),
            'data' => $manager->getData()
        ];
    }

    public function run()
    {
        $id = request('id');

        $user = \Auth::guard('user')->user();

        $otherRun = VbotJob::query()
            ->where('user_id', $user->id)
            ->where('id', '!=', $id)
            ->where('status', 1)
            ->count();
        if ($otherRun > 0) {
            throw new \Exception('只能运行一个任务，请停止其他任务后再尝试');
        }

        $vbotJob = VbotJob::query()->where('user_id', $user->id)->findOrFail($id);

        if (in_array($vbotJob->status, [-1, 1])) {
            throw new \Exception('任务异常');
        }

        try {
            $manager = new VbotManager($vbotJob);
            $manager->startJob();
        } catch (\Exception $e) {
            throw new JsonException('服务暂停维护中，请稍后再来');
        }

        return [];
    }

    public function stop()
    {
        $id = request('id');

        $user = \Auth::guard('user')->user();

        $vbotJob = VbotJob::query()->where('user_id', $user->id)->findOrFail($id);

        if ($vbotJob->status != 1) {
            throw new JsonException('任务不在运行中');
        }

        $manager = new VbotManager($vbotJob);
        $manager->stopJob();

        return [];
    }

    public function addSend()
    {
        $sendList = request('send_list', []);

        $id = request('id');

        $user = \Auth::guard('user')->user();

        $vbotJob = VbotJob::query()->where('user_id', $user->id)->findOrFail($id);

        $vbotJob->send_list = array_values(array_unique(array_merge($vbotJob->send_list, $sendList)));
        $vbotJob->save();

        $manager = new VbotManager($vbotJob);
        $manager->refreshTime();

        return [];
    }

    public function deleteSend()
    {
        $sendList = request('send_list', []);

        $id = request('id');

        $user = \Auth::guard('user')->user();

        $vbotJob = VbotJob::query()->where('user_id', $user->id)->findOrFail($id);

        $vbotJob->send_list = array_values(array_diff($vbotJob->send_list, $sendList));
        $vbotJob->save();

        $manager = new VbotManager($vbotJob);
        $manager->refreshTime();

        return [];
    }

    public function send()
    {
        $sendText = request('send_text', '');

        $id = request('id');

        $user = \Auth::guard('user')->user();

        $vbotJob = VbotJob::query()->where('user_id', $user->id)->findOrFail($id);

        if ($vbotJob->status != 1) {
            throw new JsonException('任务还未运行');
        }

        $manager = new VbotManager($vbotJob);

        $manager->sendText($sendText);

        return [];
    }
}