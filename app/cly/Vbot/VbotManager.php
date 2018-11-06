<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/8/5
 * Time: 上午10:40
 */

namespace Cly\Vbot;



use Cly\Process\Manager;
use Cly\Process\Process;
use Cly\Vbot\Exceptions\ApiFrequencyException;
use Cly\Vbot\Exceptions\CheckSyncException;
use Cly\Vbot\Exceptions\FetchUuidException;
use Cly\Vbot\Exceptions\InitFailException;
use Cly\Vbot\Exceptions\LoginFailedException;
use Cly\Vbot\Exceptions\LoginTimeoutException;
use Cly\Vbot\Exceptions\LogoutException;
use Modules\Common\Entities\VbotJob;

class VbotManager extends Manager
{
    /**
     * @var VbotJob
     */
    protected $vbotJob;

    /**
     * @var VbotService
     */
    protected $vbotService;

    public function __construct(VbotJob $vbotJob)
    {
        $options = [
            'redis' => 'vbot',
            'prefix' => 'manager',
            'name' => $vbotJob->id,
            'ticker' => 5,
        ];
        parent::__construct($options);
        $this->setMsgHandler('send_text', [$this, 'handleSendText']);

        $this->vbotJob = $vbotJob;
        $this->vbotService = new VbotService($vbotJob);
    }

    public function __invoke()
    {
        \DB::connection()->reconnect();
        $this->vbotJob->update(['status' => 1]);
        $this->initRedis();

        $this->initUuid();
        $this->vbotService->waitForLogin($this);
        $this->initUser();
        $this->initContacts();
        $process = new Process([
            'redis' => 'vbot',
            'prefix' => $this->getName(),
            'name' => 'msg_work'
        ]);
        $process->callable = function () {
            \DB::connection()->reconnect();
            $this->redis->hset($this->getName(), 'receive_msg_pid', getmypid());
            $this->redis->hset($this->getName(), 'message_status', 1);
            $this->vbotService->messageWork();
        };
        $this->fork($process);
        while (true) {
            $this->checkTime();
            $this->dispatchMsg();
            sleep(1);
        }
    }

    protected function checkTime()
    {
        $lastTime = $this->redis->hget($this->getName(), 'last_time');
        if (time() - $lastTime > 60 * 10) {
            $this->redis->hset($this->getName(), 'error_msg', '操作超时');
            $this->kill();
        }
    }

    protected function handleSendText(VbotManager $manager, $msg)
    {
        $sendList = $this->vbotJob->refresh()->send_list;

        $sendText = array_get($msg, 'sendText');
        if ($sendList && $sendText) {
            $this->redis->hset($this->getName(), 'send_status', 1);
            $sentList = $this->redis->smembers($this->getName() . ':sent_list');

            foreach (array_diff($sendList, $sentList) as $user) {
                $this->vbotService->sendMsgUser($user, $sendText, 'nickname');

                $this->redis->sadd($this->getName() . ':sent_list', $user);

                sleep(5);
            }

            $this->redis->hset($this->getName(), 'send_status', 0);
            $this->refreshTime();
        }
    }

    public function refreshTime()
    {
        $this->redis->hset($this->getName(), 'last_time', time());
    }

    public function startJob()
    {
        $this->refreshTime();
        VbotDeamon::sendVbotJob($this->vbotJob);
    }

    public function stopJob()
    {
        $this->redis->hset($this->getName(), 'status', -3);
        $this->kill();
    }

    public function sendText($sendText)
    {
        $this->sendMsg([
            'name' => 'send_text',
            'sendText' => $sendText
        ]);
    }

    public function SIGCHLD()
    {
        echo $this->getName() . ' pid:' . $this->pid . ' receive:SIGCHLD' . PHP_EOL;
        $pid = pcntl_waitpid(-1, $status, WNOHANG);
        if ($pid) {
            $process = $this->children[$pid];
            unset($this->children[$pid]);
            if ($process->name == 'msg_work') {
                $this->kill();
            }
        }
    }

    protected function initRedis()
    {
        //读已发送列表
        if ($this->vbotJob->sent_list) {
            $this->redis->sadd($this->getName() . ':sent_list', $this->vbotJob->sent_list);
        }
        $this->redis->hset($this->getName(), 'error_msg', '');
    }

    public function clearRedis()
    {
        $status = $this->redis->hget($this->getName(), 'status') ?: -2;

        $sentList = $this->redis->smembers($this->getName() . ':sent_list');
        $exception = $this->redis->hget($this->getName(), 'exception');
        $error_msg = $this->redis->hget($this->getName(), 'error_msg');

        $this->vbotJob->update([
            'status' => $status,
            'sent_list' => $sentList,
            'exception' => $exception,
            'error_msg' => $error_msg,
        ]);
        $this->redis->del([$this->getName() . ':sent_list']);
        parent::clearRedis();
    }

    public function handleException(\Exception $e)
    {
        $this->redis->hset($this->getName(), 'status', -2);

        $exception = $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
        $this->redis->hset($this->getName(), 'exception', $exception);

        if ($e instanceof FetchUuidException) {
            $error_msg = '获取二维码失败，请稍后尝试';
        } elseif ($e instanceof LoginTimeoutException) {
            $error_msg = '扫码登录超时';
        } elseif ($e instanceof LoginFailedException) {
            $error_msg = '登录失败，请稍后尝试';
        } elseif ($e instanceof InitFailException) {
            $error_msg = '初始化用户失败，请稍后尝试';
        } elseif ($e instanceof CheckSyncException) {
            $error_msg = '读取消息失败，可能登录信息丢失，请稍后重新登录';
        } elseif ($e instanceof LogoutException) {
            $error_msg = '用户已登出，可能登录信息丢失，请稍后重新登录';
        } elseif ($e instanceof ApiFrequencyException) {
            $error_msg = '微信接口调用限制，请稍后尝试';
        } else {
            $error_msg = '服务器错误，请稍后尝试';
        }
        $this->redis->hset($this->getName(), 'error_msg', $error_msg);
    }

    public function initUuid()
    {
        $qrcode = $this->vbotService->getQrcode();
        $this->redis->hset($this->getName(), 'uuid_status', 1);
        $this->redis->hset($this->getName(), 'qrcode', $qrcode);
    }

    public function scanStatus()
    {
        $this->redis->hset($this->getName(), 'scan_status', 1);
    }

    public function loginStatus()
    {
        $this->redis->hset($this->getName(), 'login_status', 1);
    }

    public function initUser()
    {
        $this->vbotService->init();

        $this->redis->hset($this->getName(), 'init_status', 1);
    }

    public function initContacts()
    {
        $this->vbotService->initContact();

        $friends = vbot('friends');
        $groups = vbot('groups');
        $members = vbot('members');
        $officials = vbot('officials');
        $specials = vbot('specials');
        $myself = vbot('myself');

        $this->redis->hset($this->getName(), 'friends', json_encode($friends->toArray()));
        $this->redis->hset($this->getName(), 'groups', json_encode($groups->toArray()));
        $this->redis->hset($this->getName(), 'members', json_encode($members->toArray()));
        $this->redis->hset($this->getName(), 'officials', json_encode($officials->toArray()));
        $this->redis->hset($this->getName(), 'specials', json_encode($specials->toArray()));
        $this->redis->hset($this->getName(), 'myself', json_encode($myself->toArray()));

        $this->redis->hset($this->getName(), 'contacts_status', 1);
    }

    public function getData()
    {
        return [
            'uuid_status' => $this->redis->hget($this->getName(), 'uuid_status'),
            'scan_status' => $this->redis->hget($this->getName(), 'scan_status'),
            'login_status' => $this->redis->hget($this->getName(), 'login_status'),
            'init_status' => $this->redis->hget($this->getName(), 'init_status'),
            'contacts_status' => $this->redis->hget($this->getName(), 'contacts_status'),
            'message_status' => $this->redis->hget($this->getName(), 'message_status'),
            'send_status' => $this->redis->hget($this->getName(), 'send_status'),
            'qrcode' => $this->redis->hget($this->getName(), 'qrcode'),
            'friends' => json_decode($this->redis->hget($this->getName(), 'friends'), true),
            'groups' => json_decode($this->redis->hget($this->getName(), 'groups'), true),
            'members' => json_decode($this->redis->hget($this->getName(), 'members'), true),
            'officials' => json_decode($this->redis->hget($this->getName(), 'officials'), true),
            'specials' => json_decode($this->redis->hget($this->getName(), 'specials'), true),
            'myself' => json_decode($this->redis->hget($this->getName(), 'myself'), true),
            'sent_list' => $this->redis->smembers($this->getName() . ':sent_list'),
        ];
    }
}