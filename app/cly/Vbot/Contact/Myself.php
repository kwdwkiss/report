<?php

namespace Cly\Vbot\Contact;

use Cly\Vbot\Support\Content;
use Monolog\Handler\StreamHandler;

class Myself
{
    public $nickname;

    public $username;

    public $uin;

    public $sex;

    public function init($user)
    {
        $this->nickname = Content::emojiHandle($user['NickName']);
        $this->username = $user['UserName'];
        $this->sex = $user['Sex'];
        $this->uin = $user['Uin'];

        $this->log();

        $this->setPath();
        $this->setLog();
    }

    private function log()
    {
        vbot('console')->log('current user\'s nickname:' . $this->nickname);
        vbot('console')->log('current user\'s username:' . $this->username);
        vbot('console')->log('current user\'s uin:' . $this->uin);
    }

    private function setPath()
    {
        $path = vbot('config')['user_path'];

        vbot('config')['user_path'] = $path . $this->uin . DIRECTORY_SEPARATOR;

        if (!is_dir(vbot('config')['user_path']) && $this->uin) {
            mkdir(vbot('config')['user_path'], 0755, true);
        }
    }

    private function setLog()
    {
        if (vbot('config')->get('log.enable', false)) {
            vbot('log')->pushHandler(new StreamHandler(
                vbot('config')->get('log.system') . DIRECTORY_SEPARATOR . $this->uin . DIRECTORY_SEPARATOR . 'vbot.log',
                vbot('config')->get('log.level'),
                true,
                vbot('config')->get('log.permission')
            ));

            vbot('messageLog')->pushHandler(new StreamHandler(
                vbot('config')->get('log.message') . DIRECTORY_SEPARATOR . $this->uin . DIRECTORY_SEPARATOR . 'message.log',
                vbot('config')->get('log.level'),
                true,
                vbot('config')->get('log.permission')
            ));
        }
    }

    public function toArray()
    {
        return [
            'nickname' => $this->nickname,
            'username' => $this->username,
            'uin' => $this->uin,
            'sex' => $this->sex,
        ];
    }
}
