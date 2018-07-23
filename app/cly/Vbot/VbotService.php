<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/7/23
 * Time: 下午5:40
 */

namespace Cly\Vbot;


use App\User;
use App\VbotJob;
use Cly\Vbot\Foundation\Vbot;

class VbotService
{
    /**
     * @var Vbot
     */
    protected $vbot;

    /**
     * @var User
     */
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;

        $path = storage_path('vbot/');
        $this->vbot = new Vbot([
            'path' => $path,
            /*
             * swoole 配置项（执行主动发消息命令必须要开启，且必须安装 swoole 插件）
             */
            'swoole' => [
                'status' => false,
                'ip' => '127.0.0.1',
                'port' => '8866',
            ],
            /*
             * 下载配置项
             */
            'download' => [
                'image' => false,
                'voice' => false,
                'video' => false,
                'emoticon' => false,
                'file' => false,
                'emoticon_path' => $path . 'emoticons', // 表情库路径（PS：表情库为过滤后不重复的表情文件夹）
            ],
            /*
             * 输出配置项
             */
            'console' => [
                'output' => true, // 是否输出
                'message' => true, // 是否输出接收消息 （若上面为 false 此处无效）
            ],
            /*
             * 日志配置项
             */
            'log' => [
                'enable' => false,
                'level' => 'debug',
                'permission' => 0777,
                'system' => $path . 'log', // 系统报错日志
                'message' => $path . 'log', // 消息日志
            ],
            /*
             * 缓存配置项
             */
            'cache' => [
                'default' => 'file', // 缓存设置 （支持 redis 或 file）
                'stores' => [
                    'file' => [
                        'driver' => 'file',
                        'path' => $path . 'cache',
                    ],
                    'redis' => [
                        'driver' => 'redis',
                        'connection' => 'default',
                    ],
                ],
            ],
            'session' => $user->id
        ]);
    }

    public function session($sessionKey)
    {
        $this->vbot->config->set('session', $sessionKey);
        return $this;
    }

    public function getQrcode()
    {
        $user = $this->user;

        $vbotJob = VbotJob::query()
            ->where('user_id', $user->id)
            ->where('status', 2)
            ->first();

        if ($vbotJob) {
            $uuid = $vbotJob->uuid;
        } else {
            $uuid = $this->vbot->server->getUuid();

            $vbotJob = VbotJob::create([
                'user_id' => $user->id,
                'status' => 2,
                'uuid' => $uuid,
                'session_key' => $user->id
            ]);
        }

        return $this->vbot->server->getQrCode($uuid);
    }

    public function userClear()
    {

    }
}