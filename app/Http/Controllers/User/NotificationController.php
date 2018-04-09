<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/4/9
 * Time: 下午4:02
 */

namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function notificationList()
    {
        $user = \Auth::guard('user')->user();

        if ($user) {
            $notifications = $user->notifications()
                ->orderBy('read_at')
                ->orderBy('created_at', 'desc')
                ->paginate();
            return NotificationResource::collection($notifications);
        } else {
            return [];
        }
    }

    public function unreadNotificationList()
    {
        $user = \Auth::guard('user')->user();

        if ($user) {
            $unreadNotifications = $user->unreadNotifications()->paginate();
            return NotificationResource::collection($unreadNotifications);
        } else {
            return [];
        }
    }

    public function readNotification()
    {
        $user = \Auth::guard('user')->user();

        $id = request('id');

        $notification = DatabaseNotification::where('notifiable_id', $user->id)->findOrFail($id);
        $notification->markAsRead();

        return [];
    }
}