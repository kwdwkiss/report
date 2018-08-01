<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VbotJob extends Model
{
    protected $guarded = [];

    protected $casts = [
        'context' => 'array'
    ];

    public static $statusType = [
        -2 => '异常退出',
        -1 => '已完成',
        0 => '已创建',
        1 => '等待扫码',
        2 => '正在发送消息',
        3 => '执行完毕，等待处理',
        10 => '已派发任务',
    ];
}
