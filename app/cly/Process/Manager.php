<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/8/9
 * Time: 下午1:01
 */

namespace Cly\Process;


class Manager extends Process
{
    public $prefix = 'manager';

    protected $children = [];

    public function fork(Process $process)
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            throw new \Exception('pcntl_fork error');
        } elseif ($pid == 0) {
            $process->run();
        }
        if ($process->waitReady()) {
            $this->children[$process->pid] = $process;
        } else {
            posix_kill($pid, SIGTERM);
        }
    }

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->setHandler(SIGCHLD, [$this, 'SIGCHLD']);
    }

    public function SIGCHLD()
    {
        echo $this->getName() . ' pid:' . $this->pid . ' receive:SIGCHLD' . PHP_EOL;
        $pid = pcntl_waitpid(-1, $status, WNOHANG);
        unset($this->children[$pid]);
    }

    public function exit()
    {
        foreach ($this->children as $key => $process) {
            $pid = $process->pid;
            if (empty($pid)) {
                echo $this->getName() . ' kill pid:' . $pid . ' null and unset' . PHP_EOL;
                unset($this->children[$pid]);
            } else {
                echo $this->getName() . ' pid:' . $this->pid . ' kill '
                    . $process->getName() . ' pid:' . $process->pid . PHP_EOL;
                posix_kill($pid, SIGTERM);
                $ret = pcntl_waitpid($pid, $status);
                echo $this->getName() . ' ret: ' . $ret . PHP_EOL;
                if ($ret == $pid) {
                    unset($this->children[$pid]);
                } else {
                    echo 'ret: ' . $ret . PHP_EOL;
                }
            }
        }
        parent::exit();
    }
}