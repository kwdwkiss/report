<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/7/23
 * Time: 下午5:40
 */

namespace Cly\Vbot;


use App\VbotJob;
use Cly\Vbot\Console\Console;
use Cly\Vbot\Core\ApiExceptionHandler;
use Cly\Vbot\Exceptions\InitFailException;
use Cly\Vbot\Exceptions\LoginTimeoutException;
use Cly\Vbot\Foundation\Vbot;
use Cly\Vbot\Message\FriendVerify;
use Cly\Vbot\Message\Text;
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

    public function getUuid()
    {
        $this->vbot->server->cleanCookies();
        $uuid = $this->vbot->server->getUuid();
        $qrcode = $this->vbot->server->getQrcode($uuid);

        $this->vbot->console->log("uuid:$uuid");
        $this->vbot->console->log("qrcode:$qrcode");

        $this->vbotJob->update(['login_status' => 1, 'qrcode' => $qrcode]);
    }

    public function waitForLogin(VbotManager $manager)
    {
        $retryTime = 10;
        $tip = 1;

        $this->vbot->console->log('please scan the qrCode with wechat.');
        while ($retryTime > 0) {
            $manager->dispatch();

            $url = sprintf('https://login.weixin.qq.com/cgi-bin/mmwebwx-bin/login?tip=%s&uuid=%s&_=%s', $tip, $this->vbot->config['server.uuid'], time());

            $content = $this->vbot->http->get($url, ['timeout' => 35]);

            preg_match('/window.code=(\d+);/', $content, $matches);

            $code = $matches[1];
            switch ($code) {
                case '201':

                    $this->vbotJob->update(['login_status' => 2]);

                    $this->vbot->console->log('please confirm login in wechat.');
                    $tip = 0;
                    break;
                case '200':

                    preg_match('/window.redirect_uri="(https:\/\/(\S+?)\/\S+?)";/', $content, $matches);

                    $this->vbot->config['server.uri.redirect'] = $matches[1] . '&fun=new';
                    $url = 'https://%s/cgi-bin/mmwebwx-bin';
                    $this->vbot->config['server.uri.file'] = sprintf($url, 'file.' . $matches[2]);
                    $this->vbot->config['server.uri.push'] = sprintf($url, 'webpush.' . $matches[2]);
                    $this->vbot->config['server.uri.base'] = sprintf($url, $matches[2]);

                    $this->vbot->server->getLogin();

                    $this->vbotJob->update(['login_status' => 3]);
                    $this->vbot->console->log('is login.');

                    return;
                case '408':
                    $tip = 1;
                    $retryTime -= 1;
                    sleep(1);
                    break;
                default:
                    $tip = 1;
                    $retryTime -= 1;
                    sleep(1);
                    break;
            }
        }

        $this->vbot->console->log('login time out!', Console::ERROR);

        throw new LoginTimeoutException('Login time out.');
    }

    public function init($first = true)
    {
        $this->vbot->console->log('current session: ' . $this->vbot->config['session']);
        $this->vbot->console->log('init begin.');

        $url = $this->vbot->config['server.uri.base'] . '/webwxinit?r=' . time();

        $tries = 0;
        while (true) {
            $result = $this->vbot->http->json($url, [
                'BaseRequest' => $this->vbot->config['server.baseRequest'],
            ], true);
            try {
                ApiExceptionHandler::handle($result, function ($result) {
                    $this->vbot->cache->forget('session.' . $this->vbot->config['session']);
                    $this->vbot->log->error('Init failed.' . json_encode($result));

                    throw new InitFailException('Init failed:' . $result['BaseResponse']['Ret']);
                });
            } catch (\Exception $e) {
                $tries++;
                if ($tries == 3) {
                    throw $e;
                }
                sleep(1);
                continue;
            }
            break;
        }

        $this->vbot->server->generateSyncKey($result, $first);

        $this->vbot->myself->init($result['User']);

        $this->vbot->log->info('response:' . json_encode($result));
        $this->vbot->console->log('init success.');
        $this->vbot->loginSuccessObserver->trigger();

        $this->vbotJob->update(['login_status' => 4]);

        return $result;
    }

    public function initContact()
    {
        $this->vbot->console->log('init contacts begin.');

        //$this->vbot->server->initContactList($result['ContactList']);

        $this->vbot->server->initContact();

        $this->vbot->console->log('init contacts end.');

        $friends = vbot('friends');
        $groups = vbot('groups');
        $members = vbot('members');
        $officials = vbot('officials');
        $specials = vbot('specials');
        $myself = vbot('myself');

        $updateData = compact('friends', 'groups', 'members', 'officials', 'specials', 'myself');
        $updateData['login_status'] = 5;

        $this->vbotJob->update($updateData);
    }

    public function messageWork()
    {
        $this->vbot->messageHandler->setHandler(function (Collection $message) {
            if ($message['type'] == FriendVerify::TYPE) {
                $from = $message['from'];
                vbot('friends')->setRemarkName($from['UserName'], 'A 宏海0000' . $from['NickName']);
            }
        });

        $this->vbot->console->log('message work');

        while (true) {
            if (!($checkSync = $this->vbot->messageHandler->checkSync())) {
                continue;
            }
            if (!$this->vbot->messageHandler->handleCheckSync($checkSync[0], $checkSync[1])) {
                throw new \Exception('handleCheckSync error:' . $checkSync[0]);
            }
        }
    }

    public function userClear()
    {
        $vbotJob = $this->vbotJob;
        $data = $vbotJob->data;

        $defaultText = '由于微信好友太多，我正在使用宏海清粉软件，如有打扰请包涵。';
        $text = array_get($data, 'send_text', $defaultText);

        foreach ($friends = vbot('friends') as $item) {
            try {
                Text::send($item['UserName'], $text);
                $data['send_contacts'][] = $item['NickName'];
            } catch (\Exception $e) {
                $vbotJob->update([
                    'status' => -2,
                    'context' => $this->vbot->config->all(),
                    'data' => $data,
                    'exception' => $e->getMessage() . $e->getTraceAsString()
                ]);
            }
            $this->vbot->console->log("send:{$item['NickName']}");
            sleep(1);
        }
        return;
    }
}