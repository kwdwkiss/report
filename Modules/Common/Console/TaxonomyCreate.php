<?php

namespace Modules\Common\Console;

use Illuminate\Console\Command;
use Modules\Common\Entities\Taxonomy;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TaxonomyCreate extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'taxonomy:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = [
            //parent
            ['id' => 1, 'pid' => 0, 'name' => '账号状态'],
            ['id' => 2, 'pid' => 0, 'name' => '账号类型'],
            ['id' => 3, 'pid' => 0, 'name' => '举报类型'],
            ['id' => 4, 'pid' => 0, 'name' => '会员类型'],
            ['id' => 5, 'pid' => 0, 'name' => '文章类型'],

            //children
            ['id' => 101, 'pid' => 1, 'name' => '无记录'],
            ['id' => 102, 'pid' => 1, 'name' => '有记录'],
            ['id' => 103, 'pid' => 1, 'name' => '网络实名认证'],
            ['id' => 104, 'pid' => 1, 'name' => '骗子'],
            ['id' => 105, 'pid' => 1, 'name' => '商家'],
            ['id' => 106, 'pid' => 1, 'name' => '花呗认证'],

            ['id' => 201, 'pid' => 2, 'name' => 'QQ'],
//                ['id' => 202, 'pid' => 2, 'name' => '旺旺'],
            ['id' => 203, 'pid' => 2, 'name' => '微信'],
//                ['id' => 204, 'pid' => 2, 'name' => '手机'],
//                ['id' => 205, 'pid' => 2, 'name' => '京东'],
//                ['id' => 206, 'pid' => 2, 'name' => '拼多多'],
            ['id' => 207, 'pid' => 2, 'name' => 'IS'],
//                ['id' => 301, 'pid' => 3, 'name' => '退款'],
//                ['id' => 302, 'pid' => 3, 'name' => '售后'],
//                ['id' => 303, 'pid' => 3, 'name' => '远程'],
//                ['id' => 304, 'pid' => 3, 'name' => '差评'],
//                ['id' => 305, 'pid' => 3, 'name' => '假址'],
//                ['id' => 306, 'pid' => 3, 'name' => '卖号'],
//                ['id' => 307, 'pid' => 3, 'name' => '返利'],
//                ['id' => 308, 'pid' => 3, 'name' => '闹事'],
//                ['id' => 309, 'pid' => 3, 'name' => '恶意商家'],
//                ['id' => 310, 'pid' => 3, 'name' => '其他'],
//                ['id' => 311, 'pid' => 3, 'name' => '微信单骗子'],
//                ['id' => 312, 'pid' => 3, 'name' => '改后台'],
//                ['id' => 313, 'pid' => 3, 'name' => '不收菜'],
            ['id' => 314, 'pid' => 3, 'name' => '中介骗子'],
            ['id' => 315, 'pid' => 3, 'name' => '不返款商家'],

            ['id' => 401, 'pid' => 4, 'name' => '普通会员'],
            ['id' => 402, 'pid' => 4, 'name' => '试客'],
            ['id' => 403, 'pid' => 4, 'name' => '商家'],
            ['id' => 404, 'pid' => 4, 'name' => '主持'],

            ['id' => 501, 'pid' => 5, 'name' => '宏海公告'],
            ['id' => 502, 'pid' => 5, 'name' => '电商导航'],
            ['id' => 503, 'pid' => 5, 'name' => '网络兼职'],
            ['id' => 504, 'pid' => 5, 'name' => '电商干货'],
            ['id' => 505, 'pid' => 5, 'name' => '电商服务'],
            ['id' => 506, 'pid' => 5, 'name' => '关于我们'],
        ];

        foreach ($data as $item) {
            Taxonomy::updateOrCreate($item);
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            //['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            //['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
