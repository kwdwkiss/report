<?php

namespace App;

use App\Http\Resources\AccountReportResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Config extends Model
{
    protected $table = 'config';

    protected $primaryKey = 'name';

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'data' => 'array'
    ];

    public static function get($name, $default = '')
    {
        $config = static::find($name);
        if (!$config) {
            return $default;
        }
        return $config->data;
    }

    public static function set($name, $data)
    {
        static::updateOrCreate(['name' => $name], ['data' => $data === null ? '' : $data]);
    }

    public static function getSiteStatics($refresh = false)
    {
        $data = Config::get('site.statics', '');
        if ($refresh || !$data) {
            $data = static::generateStatics();
            Config::set('site.statics', $data);
        }
        return $data;
    }

    public static function generateStatics()
    {
        $auth_member_num = User::count();
        $report_num = AccountReport::count();
        $last_24_report_num = AccountReport::where('created_at', '>', date('Y-m-d H:i:s', time() - 3600 * 24))->count();
        $last_4_report_data = AccountReportResource::collection(
            AccountReport::orderBy('created_at', 'desc')->take(4)->get())->toArray(new Request(['ip_hide' => 1]));
        return [
            'auth_member_num' => $auth_member_num,
            'report_num' => $report_num,
            'last_24_report_num' => $last_24_report_num,
            'last_4_report_data' => $last_4_report_data
        ];
    }

    public static function getSiteIndex()
    {
        return array_replace_recursive(static::$indexPageDefault, Config::get('site.index', []));
    }

    public static function setSiteIndex($data)
    {
        $data = array_replace_recursive(static::$indexPageDefault, $data);
        static::updateOrCreate(['name' => 'site.index'], ['data' => $data]);
    }

    public static $indexPageDefault = [
        'menu' => [
            ['name' => '账号查询', 'url' => ''],
            ['name' => '电商导航', 'url' => ''],
            ['name' => '网络兼职', 'url' => ''],
            ['name' => '电商干货', 'url' => ''],
            ['name' => '电商服务', 'url' => ''],
            ['name' => '关于我们', 'url' => ''],
            ['name' => '会员中心', 'url' => ''],
        ],
        'ad_top' => [
            ['img_src' => '/images/ad_kb.jpg', 'url' => ''],
            ['img_src' => '/images/ad_qt.jpg', 'url' => '']
        ],
        'ad_second' => [
            ['img_src' => '/images/ad_empty.gif', 'url' => ''],
            ['img_src' => '/images/ad_empty.gif', 'url' => ''],
            ['img_src' => '/images/ad_empty.gif', 'url' => ''],
            ['img_src' => '/images/ad_empty.gif', 'url' => ''],
        ],
        'ad_third' => [
            ['img_src' => '/images/ad_pdd.jpg', 'url' => ''],
            ['img_src' => '/images/ad_mjx.jpg', 'url' => '']
        ],
        'ad_foot' => [
            ['img_src' => '/images/ad_empty.gif', 'url' => ''],
            ['img_src' => '/images/ad_empty.gif', 'url' => ''],
            ['img_src' => '/images/ad_empty.gif', 'url' => ''],
            ['img_src' => '/images/ad_empty.gif', 'url' => ''],
        ],
        'service_qq' => [
            ['name' => '2412688374'],
            ['name' => '171337832'],
            ['name' => ''],
            ['name' => ''],
        ],
        'service_wx' => [
            ['name' => ''],
            ['name' => ''],
        ],
        'notice' => '12月1日起，凡被恶意举报超过180天的记录，可以联系网站客服免费删除',
        'article_data' => [
            [
                'type' => '电商导航',
                'url' => '',
                'data' => [
                    ['title' => '', 'url' => ''],
                    ['title' => '', 'url' => ''],
                    ['title' => '', 'url' => ''],
                    ['title' => '', 'url' => ''],
                ]
            ],
            [
                'type' => '电商干货',
                'url' => '',
                'data' => [
                    ['title' => '', 'url' => ''],
                    ['title' => '', 'url' => ''],
                    ['title' => '', 'url' => ''],
                    ['title' => '', 'url' => ''],
                ]
            ],
            [
                'type' => '网络兼职',
                'url' => '',
                'data' => [
                    ['title' => '', 'url' => ''],
                    ['title' => '', 'url' => ''],
                    ['title' => '', 'url' => ''],
                    ['title' => '', 'url' => ''],
                ]
            ],
            [
                'type' => '电商服务',
                'url' => '',
                'data' => [
                    ['title' => '', 'url' => ''],
                    ['title' => '', 'url' => ''],
                    ['title' => '', 'url' => ''],
                    ['title' => '', 'url' => ''],
                ]
            ]
        ]
    ];
}
