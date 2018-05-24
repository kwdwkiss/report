<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/4/4
 * Time: 下午2:04
 */

namespace App\Http\Controllers\Admin;


use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Jobs\SendNotification;
use App\Jobs\SendUserNotification;
use App\Message;
use App\Notifications\SiteMessage;
use App\Taxonomy;
use App\User;
use Illuminate\Notifications\DatabaseNotification;

class MessageController extends Controller
{
    public function list()
    {
        $name = request('name');

        $query = Message::query()->orderBy('id', 'desc');

        if (!is_null($name)) {
            $query->where('name', $name);
        }

        return MessageResource::collection($query->paginate());
    }

    public function create()
    {
        $name = request('name');
        $content = request('content');
        $remark = request('remark', '');
        $userSelect = request('userSelect');//1-全部用户 2-按用户类型 3-按id 4-按mobile

        if (!$name) {
            throw new JsonException('通知名称不能为空');
        }
        if (!$content) {
            throw new JsonException('通知内容不能为空');
        }
        if (!in_array($userSelect, [1, 2, 3, 4])) {
            throw new JsonException('用户选择类型错误');
        }

        $data = null;
        switch ($userSelect) {
            case 1:
                break;
            case 2:
                $type = request('userType');
                $userTypes = Taxonomy::userTypes();
                if (!in_array($type, $userTypes)) {
                    throw new JsonException('用户类型错误');
                }
                $data = $type;
                break;
            case 3:
                $data = request('userId');
                if (empty(explode(',', $data))) {
                    throw new JsonException('用户ID为空');
                }
                break;
            case 4:
                $data = request('userMobile');
                if (empty(explode(',', $data))) {
                    throw new JsonException('用户手机为空');
                }
                break;
        }

        $message = Message::create([
            'name' => $name,
            'content' => $content,
            'remark' => $remark
        ]);
        dispatch(new SendUserNotification($userSelect, $data, new SiteMessage($message)));

        return [];
    }

    public function delete()
    {
        $id = request('id');

        $message = Message::findOrFail($id);

        \DB::transaction(function () use ($message) {
            $message->delete();

            DatabaseNotification::where('data', 'like', '%"id":' . $message->id . '%')->delete();
        });

        return [];
    }
}