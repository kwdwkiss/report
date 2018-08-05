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

            //login
            $this->vbotService->getUuid();
            $this->vbotService->waitForLogin($this);

            //init
            $this->vbotService->init();

            //init contacts
            $this->vbotService->initContact();

            $this->children[] = $pid = pcntl_fork();
            if ($pid == -1) {
                throw new \Exception('pcntl_fork error');
            } elseif ($pid == 0) {
                pcntl_async_signals(true);
                pcntl_signal(SIGTERM, function () {
                    echo 'message work SIGTERM' . PHP_EOL;
                    exit();
                });
                \DB::connection()->reconnect();
                try {
                    $this->vbotService->messageWork();
                } catch (\Exception $e) {
                    $this->vbotJob->update([
                        'status' => -2,
                        'exception' => $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL,
                    ]);
                }
                exit();
            }

            while (true) {
                $this->dispatch();
                usleep(200000);
            }

        } catch (\Exception $e) {
            $this->vbotJob->update([
                'status' => -2,
                'exception' => $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL,
            ]);
            $this->redis->del([$this->key]);
            exit();
        }
    }

    public function installSig()
    {
        pcntl_async_signals(true);
        pcntl_signal(SIGTERM, function () {
            echo 'manager SIGTERM' . PHP_EOL;
            foreach ($this->children as $childPid) {
                posix_kill($childPid, SIGTERM);
            }
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
}