<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VbotJob extends Model
{
    protected $guarded = [];

    protected $casts = [
        'context' => 'array',
        'data' => 'array'
    ];

    protected $dataTemplate = [
        'contacts' => [],//联系人
        'send_text' => '',//发送的文本消息
        'send_mode' => 0,//发送模式，0-所有人，1-根据联系人列表
        'send_contacts' => [],//发送联系人列表
    ];

    public static $statusType = [
        -2 => '异常退出',
        -1 => '已完成',
        0 => '已创建',
        1 => '等待扫码',
        2 => '正在发送消息',
        3 => '执行完毕，等待处理',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->data = $this->dataTemplate;
    }
}
