<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VbotJob extends Model
{
    protected $guarded = [];

    protected $casts = [
        'context' => 'array',
        'data' => 'array',
        'friends' => 'array',
        'groups' => 'array',
        'members' => 'array',
        'officials' => 'array',
        'specials' => 'array',
        'myself' => 'array',
    ];

    protected $dataTemplate = [
        'contacts' => [],//联系人
        'send_text' => '',//发送的文本消息
        'send_mode' => 0,//发送模式，0-所有人，1-根据联系人列表
        'send_contacts' => [],//发送联系人列表
    ];

    public static $statusTypes = [
        -2 => '异常退出',
        -1 => '完成',
        0 => '待运行',
        1 => '运行中',
    ];

    public static $runStatusTypes = [
        0 => '获取UUID',
        1 => '等待扫码',
        2 => '等待登录',
        3 => '登录完成',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->data = $this->dataTemplate;
    }
}
