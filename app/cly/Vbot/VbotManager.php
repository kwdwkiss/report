<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/8/5
 * Time: 上午10:40
 */

namespace Cly\Vbot;


use App\VbotJob;
use Cly\Process\Manager;
use Cly\Process\Process;

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
            'name' => $vbotJob->id
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
            $this->dispatchMsg();
            sleep(1);
        }
    }

    protected function handleSendText(VbotManager $manager, $msg)
    {
        $sendList = array_get($msg, 'sendList');
        $sendText = array_get($msg, 'sendText');
        $this->vbotService->sendMsg($sendList, $sendText, 'username');
    }

    public function sendText($sendList, $sendText)
    {
        $msg = [
            'name' => 'send_text',
            'sendList' => $sendList,
            'sendText' => $sendText
        ];
        $this->sendMsg($msg);
    }

    public function exit(\Exception $e = null, $clearRedis = true)
    {
        if ($e) {
            $this->vbotJob->update([
                'status' => -2,
                'exception' => $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL,
            ]);
        } else {
            $this->vbotJob->update(['status' => -1]);
        }
        parent::exit($e, $clearRedis);
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
            'qrcode' => $this->redis->hget($this->getName(), 'qrcode'),
            'friends' => json_decode($this->redis->hget($this->getName(), 'friends'), true),
            'groups' => json_decode($this->redis->hget($this->getName(), 'groups'), true),
            'members' => json_decode($this->redis->hget($this->getName(), 'members'), true),
            'officials' => json_decode($this->redis->hget($this->getName(), 'officials'), true),
            'specials' => json_decode($this->redis->hget($this->getName(), 'specials'), true),
            'myself' => json_decode($this->redis->hget($this->getName(), 'myself'), true),
        ];
    }
}