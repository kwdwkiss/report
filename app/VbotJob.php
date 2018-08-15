<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VbotJob extends Model
{
    protected $guarded = [];

    protected $casts = [
        'context' => 'array',
        'data' => 'array',
        'send_list' => 'array',
        'sent_list' => 'array',
    ];

    protected $dataTemplate = [];

    public static $statusTypes = [
        -3 => '用户停止',
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
        $this->send_list = [];
        $this->sent_list = [];
    }
}
