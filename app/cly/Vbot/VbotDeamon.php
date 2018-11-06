<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/8/5
 * Time: 上午10:27
 */

namespace Cly\Vbot;


use Cly\Process\Manager;
use Illuminate\Support\Facades\Redis;
use Modules\Common\Entities\VbotJob;

class VbotDeamon extends Manager
{
    /**
     * @var VbotDeamon
     */
    protected static $instance;

    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new static([
                'redis' => 'vbot',
                'prefix' => 'deamon',
                'name' => 'global',
            ]);
        }
        return static::$instance;
    }

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->setMsgHandler('vbot_job', [$this, 'vbotJobHandler']);
    }

    public static function sendVbotJob(VbotJob $vbotJob)
    {
        $instance = static::getInstance();
        if (!$instance->isRunning()) {
            throw new \Exception($instance->getName() . ' is not running');
        }
        $msg = [
            'name' => 'vbot_job',
            'id' => $vbotJob->id,
        ];
        $instance->sendMsg($msg);
    }

    public function vbotJobHandler(VbotDeamon $deamon, $msg)
    {
        echo 'vbotJobHandler:' . json_encode($msg) . PHP_EOL;
        $id = array_get($msg, 'id');
        $vbotJob = VbotJob::query()->find($id);
        if (!in_array($vbotJob->status, [-1, 1])) {
            $manager = new VbotManager($vbotJob);
            $this->fork($manager);
        }
    }

    public function runInit()
    {
        $keys = $this->redis->keys('*');
        if (!empty($keys)) {
            Redis::resolve()->del($keys);
        }
        VbotJob::query()->where('status', 1)->update(['status' => -2]);

        parent::runInit();
    }

    public function __invoke()
    {
        while (true) {
            $this->dispatchMsg();
            sleep(1);
        }
    }
}