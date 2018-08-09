<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/8/5
 * Time: 上午10:40
 */

namespace Cly\Vbot;


use App\VbotJob;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;

class VbotManager
{
    /**
     * @var Connection
     */
    protected $redis;

    protected $key;

    /**
     * @var VbotJob
     */
    protected $vbotJob;

    /**
     * @var VbotService
     */
    protected $vbotService;

    protected $children = [];

    public function __construct(VbotJob $vbotJob)
    {
        $this->vbotJob = $vbotJob;
        $this->vbotService = new VbotService($vbotJob);
        $this->redis = Redis::connection('vbot');
        $this->key = 'manager:' . $vbotJob->id;
    }

    public function run()
    {
        try {
            $this->installSig();

            $this->redis->hset($this->key, 'pid', getmypid());

            $this->initUuid();
            $this->vbotService->waitForLogin($this);
            $this->init();
            $this->initContacts();

            $this->children[] = $pid = pcntl_fork();
            if ($pid == -1) {
                throw new \Exception('pcntl_fork error');
            } elseif ($pid == 0) {
                pcntl_async_signals(true);
                pcntl_signal(SIGTERM, function () {
                    $this->redis->hdel($this->key, ['receive_msg_pid']);
                    echo 'message work SIGTERM' . PHP_EOL;
                    exit();
                });
                \DB::connection()->reconnect();
                $this->redis->hset($this->key, 'receive_msg_pid', getmypid());
                $this->redis->hset($this->key, 'message_status', 1);
                $this->vbotService->messageWork();
                exit();
            }

            while (true) {
                $this->dispatch();

                $sendMsgStr = $this->redis->lpop($this->key . ':queue');
                $sendMsg = json_decode($sendMsgStr, true);
                switch (array_get($sendMsg, 'type')) {
                    case 'send_msg':
                        $sendList = array_get($sendMsg, 'send_list');
                        $sendText = array_get($sendMsg, 'send_text');
                        $this->vbotService->sendMsg($sendList, $sendText, $this, 'username');
                        break;
                }

                usleep(200000);
            }

        } catch (\Exception $e) {
            $this->vbotJob->update([
                'status' => -2,
                'exception' => $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL,
            ]);
            $this->redis->del([$this->key]);
            $this->termChild();
            throw $e;
        }
    }

    public function installSig()
    {
        pcntl_async_signals(true);
        pcntl_signal(SIGTERM, function () {
            echo 'manager SIGTERM' . PHP_EOL;
            $this->termChild();
            $this->redis->del([$this->key]);
            $this->vbotJob->update(['status' => -1]);
            exit();
        });
        pcntl_signal(SIGCHLD, function () {
            echo 'manager SIGCHLD' . PHP_EOL;
            $pid = pcntl_waitpid(-1, $status);
            unset($this->children[$pid]);
        });
    }

    public function termChild()
    {
        foreach ($this->children as $childPid) {
            posix_kill($childPid, SIGTERM);
        }
    }

    public function dispatch()
    {
        $sig = $this->redis->hget($this->key, 'sig');
        switch ((string)$sig) {
            case 'sig_term':
                echo $sig . ':posix_kill ' . getmypid() . PHP_EOL;
                posix_kill(getmypid(), SIGTERM);
                break;
        }
        $this->redis->hdel($this->key, ['sig']);
    }

    public function sig($sig)
    {
        $this->redis->hset($this->key, 'sig', $sig);
    }

    public function sigTerm()
    {
        $this->sig('sig_term');
    }

    public function sigMsg(array $sendList, string $sendText)
    {
        $dataStr = json_encode([
            'type' => 'send_msg',
            'send_list' => $sendList,
            'send_text' => $sendText,
        ]);
        $this->redis->rpush($this->key . ':queue', [$dataStr]);
    }

    public function initUuid()
    {
        $qrcode = $this->vbotService->getQrcode();
        $this->redis->hset($this->key, 'uuid_status', 1);
        $this->redis->hset($this->key, 'qrcode', $qrcode);
    }

    public function scanStatus()
    {
        $this->redis->hset($this->key, 'scan_status', 1);
    }

    public function loginStatus()
    {
        $this->redis->hset($this->key, 'login_status', 1);
    }

    public function init()
    {
        $this->vbotService->init();

        $this->redis->hset($this->key, 'init_status', 1);
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

        $this->redis->hset($this->key, 'friends', json_encode($friends->toArray()));
        $this->redis->hset($this->key, 'groups', json_encode($groups->toArray()));
        $this->redis->hset($this->key, 'members', json_encode($members->toArray()));
        $this->redis->hset($this->key, 'officials', json_encode($officials->toArray()));
        $this->redis->hset($this->key, 'specials', json_encode($specials->toArray()));
        $this->redis->hset($this->key, 'myself', json_encode($myself->toArray()));

        $this->redis->hset($this->key, 'contacts_status', 1);
    }

    public function getData()
    {
        return [
            'uuid_status' => $this->redis->hget($this->key, 'uuid_status'),
            'scan_status' => $this->redis->hget($this->key, 'scan_status'),
            'login_status' => $this->redis->hget($this->key, 'login_status'),
            'init_status' => $this->redis->hget($this->key, 'init_status'),
            'contacts_status' => $this->redis->hget($this->key, 'contacts_status'),
            'message_status' => $this->redis->hget($this->key, 'message_status'),
            'qrcode' => $this->redis->hget($this->key, 'qrcode'),
            'friends' => json_decode($this->redis->hget($this->key, 'friends'), true),
            'groups' => json_decode($this->redis->hget($this->key, 'groups'), true),
            'members' => json_decode($this->redis->hget($this->key, 'members'), true),
            'officials' => json_decode($this->redis->hget($this->key, 'officials'), true),
            'specials' => json_decode($this->redis->hget($this->key, 'specials'), true),
            'myself' => json_decode($this->redis->hget($this->key, 'myself'), true),
        ];
    }
}