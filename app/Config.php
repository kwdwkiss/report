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

    public static function getBasic()
    {
        $basicList = [
            'domain',
            'name',
            'close_register',
            'index_blog_article',
            'seo_keywords',
            'seo_description',
            'close_recharge',
            'close_recharge_text',
        ];
        $basic = [];

        \DB::transaction(function () use (&$basic, $basicList) {
            foreach ($basicList as $name) {
                $basic[$name] = Config::get('site.' . $name);
            }
        });
        return $basic;
    }

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
            AccountReport::where('display', 1)->orderBy('created_at', 'desc')->take(4)->get())->toArray(new Request(['ip_hide' => 1]));
        return [
            'auth_member_num' => $auth_member_num,
            'report_num' => $report_num,
            'last_24_report_num' => $last_24_report_num,
            'last_4_report_data' => $last_4_report_data
        ];
    }

    public static function getSitePopWindow()
    {
        return array_replace_recursive(static::$popWindowDefault, static::get('site.pop-window', []));
    }

    public static function setSitePopWindow($data)
    {
        static::set('site.pop-window', array_replace_recursive(static::$popWindowDefault, $data));
    }

    public static $popWindowDefault = ['title' => '', 'content' => ''];

    public static function getSiteIndex()
    {
        return array_replace_recursive(static::$indexPageDefault, static::get('site.index', []));
    }

    public static function setSiteIndex($data)
    {
        static::set('site.index', array_replace_recursive(static::$indexPageDefault, $data));
    }

    public static $indexPageDefault = [
        'menu' => [
            ['name' => '账号查询', 'url' => '/#/'],
            ['name' => '电商导航', 'url' => '/#/article/list/502'],
            ['name' => '网络兼职', 'url' => '/#/article/list/503'],
            ['name' => '电商干货', 'url' => '/#/article/list/504'],
            ['name' => '电商服务', 'url' => '/#/article/list/505'],
            ['name' => '关于我们', 'url' => '/#/article/list/506'],
            ['name' => '会员中心', 'url' => '/#/'],
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
        'mobile_ad' => [
            'report_search_top' => ['img_src' => '/images/ad_empty.png', 'url' => ''],
            'report_search' => ['img_src' => '/images/ad_empty.png', 'url' => ''],
            'check_tb' => ['img_src' => '/images/ad_empty.png', 'url' => ''],
            'check_pdd' => ['img_src' => '/images/ad_empty.png', 'url' => ''],
            'check_jd' => ['img_src' => '/images/ad_empty.png', 'url' => ''],
            'login' => ['img_src' => '/images/ad_empty.png', 'url' => ''],
            'one_key_excel' => ['img_src' => '/images/ad_empty.png', 'url' => ''],
            'wx_clear_friends' => ['img_src' => '/images/ad_empty.png', 'url' => ''],
        ],
        'service_qq' => [
            ['name' => '2412688374'],
            ['name' => '171337832'],
            ['name' => ''],
            ['name' => ''],
        ],
        'service_wx_id' => [
            ['name' => ''],
            ['name' => ''],
        ],
        'service_wx' => [
            ['name' => '/images/wx_kf.png'],
            ['name' => '/images/wx_kf.png'],
        ],
        'notice' => [
            'title' => '12月1日起，凡被恶意举报超过180天的记录，可以联系网站客服免费删除',
            'url' => '',
            'moreUrl' => '/#/article/list/501'
        ],
        'article_data' => [
            [
                'type' => '电商导航',
                'url' => '/#/article/list/502',
                'data' => [
                    ['title' => '全国最大的买家秀平台', 'url' => '', 'created_at' => ''],
                    ['title' => '全国最大的淘宝客推广平台', 'url' => '', 'created_at' => ''],
                    ['title' => '一个比较不错的黑搜索技术公司', 'url' => '', 'created_at' => ''],
                    ['title' => '最安全的补单平台', 'url' => '', 'created_at' => ''],
                ]
            ],
            [
                'type' => '电商干货',
                'url' => '/#/article/list/503',
                'data' => [
                    ['title' => '新店开业前3个月怎么办？看大神如何玩转教你', 'url' => '', 'created_at' => ''],
                    ['title' => '看大神如何优化店铺转化率与关键词', 'url' => '', 'created_at' => ''],
                    ['title' => '刷单违法后，看各路淘宝店的全新推广手段', 'url' => '', 'created_at' => ''],
                    ['title' => '如何利用各种新媒体打开店铺的销量', 'url' => '', 'created_at' => ''],
                ]
            ],
            [
                'type' => '网络兼职',
                'url' => '/#/article/list/504',
                'data' => [
                    ['title' => '秒赚10元任务，时间2分钟，0风险', 'url' => '', 'created_at' => ''],
                    ['title' => '秒赚24元任务，时间2分钟，0风险', 'url' => '', 'created_at' => ''],
                    ['title' => '理财小兼职，投资48天回本，往后每月赚50%', 'url' => '', 'created_at' => ''],
                    ['title' => '消费者联盟兼职，每月收益没上限，每小时20元', 'url' => '', 'created_at' => ''],
                ]
            ],
            [
                'type' => '电商服务',
                'url' => '/#/article/list/505',
                'data' => [
                    ['title' => '品牌商代运营服务', 'url' => '', 'created_at' => ''],
                    ['title' => '淘宝京东新店顾问指导', 'url' => '', 'created_at' => ''],
                    ['title' => '公众号的申请与吸粉', 'url' => '', 'created_at' => ''],
                    ['title' => '微信电商小程序开发', 'url' => '', 'created_at' => ''],
                ]
            ]
        ]
    ];
}
