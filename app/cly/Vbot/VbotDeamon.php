<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/8/5
 * Time: 上午10:27
 */

namespace Cly\Vbot;


use App\VbotJob;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;

class VbotDeamon
{
    /**
     * @var Connection
     */
    protected $redis;

    protected $key;

    protected $children = [];

    public function __construct()
    {
        $this->redis = Redis::connection('vbot');
        $this->key = 'deamon';
    }

    public function run()
    {
        $this->installSig();

        $this->redis->hset($this->key, 'pid', getmypid());

        while (true) {
            $id = $this->pop();
            $vbotJob = VbotJob::find($id);

            if ($vbotJob && $vbotJob->status == 0) {

                $this->children[] = $pid = pcntl_fork();
                if ($pid == -1) {
                    throw new \Exception('pcntl_fork error');
                } elseif ($pid == 0) {
                    \DB::connection()->reconnect();
                    $vbotJob->update(['status' => 1]);
                    $vbotManager = new VbotManager($vbotJob);
                    $vbotManager->run();
                    exit();
                }

            }

            $this->dispatch();
            usleep(200000);
        }
    }

    public function installSig()
    {
        pcntl_async_signals(true);
        pcntl_signal(SIGTERM, function () {
            echo 'deamon SIGTERM' . PHP_EOL;
            foreach ($this->children as $childPid) {
                posix_kill($childPid, SIGTERM);
            }
            $this->redis->del([$this->key]);
            exit();
        });
        pcntl_signal(SIGCHLD, function () {
            echo 'deamon SIGCHLD' . PHP_EOL;
            $pid = pcntl_waitpid(-1, $status);
            unset($this->children[$pid]);
        });
    }

    public function dispatch()
    {
        $sig = $this->redis->hget($this->key, 'sig');
        switch ((string)$sig) {
            case 'sig_term':
                echo 'deamon sig_term' . PHP_EOL;
                posix_kill(getmypid(), SIGTERM);
                break;
        }
        $this->redis->hdel($this->key, ['sig']);
    }

    public function sig($sig)
    {
        $this->redis->hset($this->key, 'sig', $sig);
    }

    public function push(VbotJob $vbotJob)
    {
        $this->redis->rpush($this->key . ':queue', [$vbotJob->id]);
    }

    public function pop()
    {
        return $this->redis->lpop($this->key . ':queue');
    }
}