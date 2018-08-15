<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/8/9
 * Time: 下午1:00
 */

namespace Cly\Process;


use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;

/**
 * @property string pid
 */
class Process
{
    /**
     * @var Connection
     */
    public $redis;

    public $prefix = 'process';

    public $name;

    public $ticker = 1;

    /**
     * @var callable
     */
    protected $callable;

    protected $handlers = [];

    protected $msgHandlers = [];

    public function __construct(array $options = [])
    {
        foreach ($options as $key => $value) {
            $this->$key = $value;
        }

        $this->redis = Redis::resolve($this->redis);
        if (!$this->name) {
            $this->name = str_random(6);
        }
        if (!$this->callable && is_callable($this)) {
            $this->callable = $this;
        }

        $this->setHandler(SIGALRM, [$this, 'SIGALRM']);
        $this->setHandler(SIGTERM, [$this, 'SIGTERM']);
        $this->setHandler(SIGINT, [$this, 'SIGINT']);
    }

    public function setCallable(callable $callable)
    {
        $this->callable = $callable;
        return $this;
    }

    public function run()
    {
        try {
            $this->runInit();

            echo $this->getName() . ' pid:' . $this->pid . ' run' . PHP_EOL;

            call_user_func($this->callable, $this);

        } catch (\Exception $e) {
            $this->handleException($e);
        } finally {
            $this->clearRedis();
            $this->exit();
        }
    }

    public function runInit()
    {
        //运行前报错，不清除redis，防止修改正在运行的进程
        try {
            if (!$this->callable) {
                throw new \Exception($this->getName() . ' callable null');
            }
            if ($this->isRunning()) {
                throw new \Exception($this->getName() . ' is Running');
            }
        } catch (\Exception $e) {
            $this->handleException($e);
            $this->exit();
        }

        $this->pid = getmypid();
        $this->installSigHandler();
    }

    public function waitReady()
    {
        $i = 0;
        while ($i < 10) {
            if ($this->pid) {
                return true;
            }
            $i++;
            usleep(100000);
        }
        return false;
    }

    public function installSigHandler()
    {
        pcntl_async_signals(true);
        foreach ($this->handlers as $sig => $callable) {
            if ($sig == SIGALRM) {
                pcntl_signal($sig, $callable);
                pcntl_alarm($this->ticker);
            } else {
                pcntl_signal($sig, $callable);
            }
        }
    }

    public function setHandler(int $sig, callable $callable)
    {
        if ($sig < 1 || $sig > 31) {
            throw new \Exception("signal:$sig error");
        }
        $this->handlers[$sig] = $callable;
    }

    public function setMsgHandler(string $name, callable $callable)
    {
        $this->msgHandlers[$name] = $callable;
    }

    public function dispatchMsg()
    {
        $msgStr = $this->redis->lpop($this->getMsgKey());
        $msg = json_decode($msgStr, true);
        $name = array_get($msg, 'name');
        if (isset($this->msgHandlers[$name])) {
            try {
                call_user_func($this->msgHandlers[$name], $this, $msg);
            } catch (\Exception $e) {
                echo $e->getMessage() . PHP_EOL;
            }
        }
    }

    public function sendMsg(array $msg)
    {
        $this->redis->rpush($this->getMsgKey(), [json_encode($msg)]);
    }

    public function kill($sig = SIGTERM)
    {
        posix_kill($this->pid, $sig);
    }

    public function SIGALRM()
    {
        //中断sleep，网络请求，继续执行
        //echo 'SIGALRM: ' . time() . PHP_EOL;
        pcntl_alarm($this->ticker);
    }

    public function SIGTERM()
    {
        echo $this->getName() . ' pid:' . $this->pid . ' receive:SIGTERM' . PHP_EOL;
        $this->clearRedis();
        $this->exit();
    }

    public function SIGINT()
    {
        echo $this->getName() . ' pid:' . $this->pid . ' receive:SIGINT' . PHP_EOL;
        $this->clearRedis();
        $this->exit();
    }

    public function exit()
    {
        echo $this->getName() . ' pid:' . $this->pid . ' exit ' . PHP_EOL;
        exit();
    }

    public function handleException(\Exception $e)
    {
        echo $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
    }

    public function clearRedis()
    {
        $this->redis->del([$this->getName()]);
    }

    public function isRunning()
    {
        return $this->redis->hget($this->getName(), 'pid');
    }

    public function getName()
    {
        return $this->prefix . ':' . $this->name;
    }

    public function getMsgKey()
    {
        return $this->getName() . ':msg';
    }

    public function __get($name)
    {
        return $this->redis->hget($this->getName(), $name);
    }

    public function __set($name, $value)
    {
        $this->redis->hset($this->getName(), $name, $value);
    }
}