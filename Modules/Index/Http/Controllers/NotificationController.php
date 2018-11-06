<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/4/9
 * Time: 下午4:02
 */

namespace Modules\Index\Http\Controllers;


use Modules\Common\Exceptions\JsonException;
use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\NotificationResource;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function notificationList()
    {
        $user = \Auth::guard('user')->user();
        if (!$user) {
            throw new JsonException('用户未登录');
        }

        $notifications = $user->notifications()
            ->orderBy('read_at')
            ->orderBy('created_at', 'desc')
            ->paginate();
        return NotificationResource::collection($notifications);
    }

    public function unreadNotificationList()
    {
        $user = \Auth::guard('user')->user();
        if (!$user) {
            throw new JsonException('用户未登录');
        }

        $unreadNotifications = $user->unreadNotifications()->paginate();
        return NotificationResource::collection($unreadNotifications);
    }

    public function unreadNotificationCount()
    {
        $user = \Auth::guard('user')->user();
        if (!$user) {
            throw new JsonException('用户未登录');
        }

        $count = $user->unreadNotifications()->count();

        return ['data' => $count];
    }

    public function readNotification()
    {
        $user = \Auth::guard('user')->user();

        $id = request('id');

        $notification = DatabaseNotification::where('notifiable_id', $user->id)->findOrFail($id);
        if ($notification->unread()) {
            $notification->markAsRead();
        }

        return [];
    }
}