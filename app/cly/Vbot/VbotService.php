<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/7/23
 * Time: 下午5:40
 */

namespace Cly\Vbot;


use App\VbotJob;
use Cly\Vbot\Foundation\Vbot;
use Cly\Vbot\Message\FriendVerify;
use Cly\Vbot\Message\Text;
use Illuminate\Config\Repository;
use Illuminate\Support\Collection;

class VbotService
{
    /**
     * @var Vbot
     */
    protected $vbot;

    /**
     * @var VbotJob
     */
    protected $vbotJob;

    public function __construct($vbotJob)
    {
        $this->vbotJob = $vbotJob;

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
                'enable' => true,
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
            'session' => $vbotJob->user_id
        ]);
    }

    public function userClear()
    {
        $vbotJob = $this->vbotJob;
        //还原上下文
        if ($vbotJob->context) {
            $this->vbot->config = new Repository($vbotJob->context);
        }

        $uuid = array_get($vbotJob->context, 'server.uuid');
        //新任务，获取uuid
        if ($vbotJob->status == 0 || empty($uuid)) {
            $this->vbot->server->cleanCookies();
            $uuid = $this->vbot->server->getUuid();
            echo "new job get uuid:$uuid\n";
            $vbotJob->update([
                'status' => 1,
                'context' => $this->vbot->config->all(),
                'qrcode_url' => $this->vbot->server->getQrcode($uuid),
            ]);
        }

        //等待扫码登录
        if ($vbotJob->status == 1) {
            try {
                $this->vbot->server->waitForLogin();
            } catch (\Exception $e) {
                $vbotJob->update([
                    'status' => -2,
                    'context' => $this->vbot->config->all()
                ]);
                exit();
            }
            echo "is login\n";
            $vbotJob->update([
                'status' => 2,
                'context' => $this->vbot->config->all()
            ]);
        }

        //已登录，开始任务
        if ($vbotJob->status == 2) {
            $times = 3;
            while (true) {
                if ($times == 0) {
                    exit();
                }
                try {
                    $this->vbot->server->getLogin();
                    $this->vbot->server->init();
                    break;
                } catch (Exceptions\InitFailException $e) {
                    echo $e->getMessage() . "\n";
                    $times--;
                }
            }

            $this->vbot->messageHandler->setHandler(function (Collection $message) {
                if ($message['type'] == FriendVerify::TYPE) {
                    $from = $message['from'];
                    vbot('friends')->setRemarkName($from['UserName'], 'aa' . $from['NickName']);
                }
            });

            $pid = pcntl_fork();
            if ($pid == -1) {
                throw new \Exception('could not fork');
            } elseif ($pid) {
                //parent
                echo "parent child:$pid\n";

                $closeTime = 0;
                $ret = 0;
                while (true) {
                    if (!$closeTime) {
                        $ret = pcntl_waitpid($pid, $status, WNOHANG);
                        echo "ret:$ret\n";
                    }
                    if ($ret == -1) {
                        break;
                    } elseif ($ret > 0) {
                        $ret = 0;
                        $closeTime = time();
                        $closeTimeStr = date('H:i:s', $closeTime);
                        echo "child close at $closeTimeStr\n";
                    }
                    if ($closeTime && time() - $closeTime > 60) {
                        $now = date('H:i:s', time());
                        echo "parent close at $now\n";

                        $vbotJob->update([
                            'status' => -1,
                            'context' => $this->vbot->config->all()
                        ]);
                        break;
                    }
                    echo "fetch message\n";
                    if (!($checkSync = $this->vbot->messageHandler->checkSync())) {
                        continue;
                    }
                    if (!$this->vbot->messageHandler->handleCheckSync($checkSync[0], $checkSync[1])) {
                        break;
                    }
                }
            } else {
                //child
                $pid = posix_getpid();
                echo "child $pid\n";

                $nicknameList = [
                    '宏海网络-微信收款'
                ];
                foreach ($friends = vbot('friends') as $item) {
                    if (in_array($item['NickName'], $nicknameList)) {
                        Text::send($item['UserName'], '测试是否还是好友');
                        echo "send:{$item['UserName']}\n";
                    }
                }
                $vbotJob->update([
                    'status' => 3,
                    'context' => $this->vbot->config->all()
                ]);
                exit();
            }
        }
    }

    public function error()
    {
        $this->vbotJob->update(['status' => -2]);
    }
}