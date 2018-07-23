<?php

namespace App\Console\Commands;

use Cly\Vbot\Foundation\Vbot;
use Cly\Vbot\Message\FriendVerify;
use Cly\Vbot\Message\Text;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class VbotCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vbot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = storage_path('vbot/');
        $vbot = new Vbot([
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
            'session' => 1,
        ]);
        $vbot->observer->setQrCodeObserver(function ($url) {
            $this->line($url);
        });
        $vbot->messageHandler->setHandler(function (Collection $message) {
            if ($message['type'] == FriendVerify::TYPE) {
                $from = $message['from'];
                vbot('friends')->setRemarkName($from['UserName'], 'aa' . $from['NickName']);
            }
        });

        $server = $vbot->server;
        if (!$server->tryLogin()) {
            $server->cleanCookies();
            $server->login();
        }
        $server->init();

        //Text::send('宏海网络-微信收款', '测试是否还是好友');
        $nicknameList = [
            '宏海网络-微信收款'
        ];

        foreach ($friends = vbot('friends') as $item) {
            if (in_array($item['NickName'], $nicknameList)) {
                Text::send($item['UserName'], '测试是否还是好友');
                $this->line(json_encode($item));
            }
        }

        $vbot->messageHandler->listen();
    }
}
