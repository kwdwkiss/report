<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::middleware([])->group(function () {
    Route::get('/emulator_tb/index', 'Index\EmulatorTbController@index');
    Route::post('/emulator_tb/member/request_nick_check.do',
        'Index\EmulatorTbController@member_request_nick_check');
    Route::post('/emulator_tb/member/login.jhtml',
        'Index\EmulatorTbController@member_login');
});

Route::middleware(['csrf'])->group(function () {
    Route::get('/check_tb', 'Index\CheckAccountController@tb');
    Route::get('/check_pdd', 'Index\CheckAccountController@pdd');
    Route::get('/check_jd', 'Index\CheckAccountController@jd');
    Route::get('/check_geo', 'Index\CheckAccountController@geo');

//    Route::get('/emulator_pdd/login', 'Index\EmulatorPddController@login');
//    Route::post('/emulator_pdd/sms', 'Index\EmulatorPddController@sms');
//    Route::post('/emulator_pdd/do-login', 'Index\EmulatorPddController@doLogin');
//
//    Route::get('/emulator_pdd_mms/login', 'Index\EmulatorPddMmsController@login');
//    Route::get('/emulator_pdd_mms/captcha', 'Index\EmulatorPddMmsController@captcha');
//    Route::post('/emulator_pdd_mms/sms', 'Index\EmulatorPddMmsController@sms');
//    Route::post('/emulator_pdd_mms/do-login', 'Index\EmulatorPddMmsController@doLogin');

    Route::get('/', 'Index\IndexController@index')->name('login');
    Route::get('/index/pop-window', 'Index\IndexController@popWindow');
    Route::get('/index/index/recharge_url', 'Index\IndexController@rechargeUrl');
    Route::post('/index/upload-oss', 'Index\IndexController@uploadOss');
    Route::post('/index/behavior-log', 'Index\IndexController@behaviorLog');
    Route::post('/index/one-key-excel', 'Index\IndexController@oneKeyExcel');
    Route::get('/index/excel_cost_type', 'Index\IndexController@excelCostType');

    Route::get('/index/article/list', 'Index\ArticleController@list');
    Route::get('/index/article/show', 'Index\ArticleController@show');

    Route::get('/index/index/basic', 'Index\IndexController@basic');

    Route::get('/taxonomy/all/data', 'Index\TaxonomyController@allData');
    Route::get('/taxonomy/all/display', 'Index\TaxonomyController@allDisplay');

    Route::get('/index/product/index', 'Index\ProductController@index');
    Route::get('/index/product/show', 'Index\ProductController@show');

    Route::get('/user/logout', 'User\UserController@logout');
    Route::post('/user/login', 'User\UserController@login');
    Route::post('/user/forget-password', 'User\UserController@forgetPassword');
    Route::post('/user/register', 'User\UserController@register');
    Route::post('/user/sms', 'User\UserController@sms');

    Route::get('/user/recharge/callback', 'User\RechargeController@apiCallback');
    Route::get('/user/recharge/page-callback', 'User\RechargeController@pageCallback');
});

Route::middleware(['auth:user', 'csrf'])->group(function () {
    Route::get('/user/info', 'User\UserController@info');
    Route::post('/index/search', 'Index\IndexController@search');
    Route::post('/index/report', 'Index\IndexController@report');
    Route::get('/user/recharge/index', 'User\RechargeController@index');
    Route::get('/user/amount/index', 'User\AmountController@index');

    Route::get('/index/user_product/index', 'Index\UserProductController@index');
    Route::post('/index/user_product/create', 'Index\UserProductController@create');

    Route::get('/user/notification', 'User\NotificationController@notificationList');
    Route::get('/user/unread-notification', 'User\NotificationController@unreadNotificationList');
    Route::get('/user/unread-notification/count', 'User\NotificationController@unreadNotificationCount');
    Route::post('/user/read-notification', 'User\NotificationController@readNotification');

    Route::post('/user/modify', 'User\UserController@modify');
    Route::post('/user/merchant/modify', 'User\UserController@merchantModify');

    Route::get('/user/report/index', 'User\AccountReportController@index');
    Route::post('/user/report/hide', 'User\AccountReportController@hide');

    Route::get('/user/excel/index', 'User\ExcelController@index');
    Route::post('/user/excel/create', 'User\ExcelController@create');
    Route::post('/user/excel/update', 'User\ExcelController@update');
    Route::post('/user/excel/delete', 'User\ExcelController@delete');

    Route::get('/user/user-auth-bill/index', 'User\UserAuthBillController@index');
    Route::post('/user/user-auth-bill/apply', 'User\UserAuthBillController@apply');

    Route::get('/user/vbot/index', 'User\VbotController@index');
    Route::post('/user/vbot/create', 'User\VbotController@create');
    Route::get('/user/vbot/detail', 'User\VbotController@detail');
    Route::post('/user/vbot/delete', 'User\VbotController@delete');
    Route::get('/user/vbot/status', 'User\VbotController@status');
    Route::post('/user/vbot/run', 'User\VbotController@run');
    Route::post('/user/vbot/stop', 'User\VbotController@stop');
    Route::post('/user/vbot/send', 'User\VbotController@send');
    Route::post('/user/vbot/add_send', 'User\VbotController@addSend');
    Route::post('/user/vbot/delete_send', 'User\VbotController@deleteSend');

    Route::post('/mobile/search', 'Index\MobileController@search');
});

//>>>>>>>>>admin
Route::namespace('Admin')->middleware(['domain.check', 'csrf'])->group(function () {

    Route::get('/admin', 'IndexController@index');
    Route::post('/admin/admin/login', 'AdminController@login');
    Route::get('/admin/admin/logout', 'AdminController@logout');
    Route::get('/admin/role/all', 'RoleController@all');
    Route::get('/admin/permission/all', 'PermissionController@all');
});

Route::namespace('Admin')->middleware(['domain.check', 'auth:admin', 'rbac'])->group(function () {

    Route::post('/admin/attachment/upload', 'AttachmentController@upload')->name('上传附件到本地');
    Route::post('/admin/attachment/upload_oss', 'AttachmentController@uploadOss')->name('上传附件到oss');
    Route::post('/admin/attachment/upload_oss_image', 'AttachmentController@uploadOssImage')
        ->name('上传身份证到oss加水印');
});

Route::namespace('Admin')->middleware(['domain.check', 'auth:admin', 'csrf'])->group(function () {

    Route::get('/admin/admin/info', 'AdminController@info');
    Route::post('/admin/admin/modify_password', 'AdminController@modifyPassword');
});

Route::namespace('Admin')->middleware(['domain.check', 'auth:admin', 'csrf', 'rbac'])->group(function () {

    Route::get('/admin/admin/index', 'AdminController@index')->name('管理员列表');
    Route::post('/admin/admin/create', 'AdminController@create')->name('管理员创建');
    Route::get('/admin/admin/show', 'AdminController@show')->name('管理员详情');
    Route::post('/admin/admin/update', 'AdminController@update')->name('管理员更新');
    Route::post('/admin/admin/delete', 'AdminController@delete')->name('管理员删除');

//    Route::get('/admin/permission/index', 'PermissionController@index')->name('权限列表');
    Route::post('/admin/permission/refresh', 'PermissionController@refresh')->name('权限刷新');

    Route::get('/admin/role/index', 'RoleController@index')->name('角色列表');
    Route::get('/admin/role/show', 'RoleController@show')->name('角色详情');
    Route::post('/admin/role/update', 'RoleController@update')->name('角色更新');

    Route::get('/admin/taxonomy/index', 'TaxonomyController@index')->name('分类列表');
    Route::post('/admin/taxonomy/create', 'TaxonomyController@create')->name('分类创建');
    Route::post('/admin/taxonomy/update', 'TaxonomyController@update')->name('分类更新');
    Route::post('/admin/taxonomy/delete', 'TaxonomyController@delete')->name('分类删除');

//    Route::get('/admin/tag/index', 'TagController@index')->name('标签列表');
//    Route::post('/admin/tag/create', 'TagController@create')->name('标签创建');
//    Route::post('/admin/tag/update', 'TagController@update')->name('标签更新');
//    Route::post('/admin/tag/delete', 'TagController@delete')->name('标签删除');

    Route::get('/admin/statement/profile', 'StatementController@profile')->name('报表概况');
    Route::get('/admin/statement/index', 'StatementController@index')->name('报表列表');
    Route::get('/admin/behavior_log/index', 'BehaviorLogController@index')->name('行为日志列表');

    Route::get('/admin/site/get_basic', 'SiteController@getBasic')->name('网站设置详情');
    Route::post('/admin/site/set_basic', 'SiteController@setBasic')->name('网站设置更新');

    Route::get('/admin/site/get_index', 'SiteController@getIndex')->name('首页设置详情');
    Route::post('/admin/site/set_index', 'SiteController@setIndex')->name('首页设置更新');
    Route::post('/admin/site/pop_window', 'SiteController@popWindow')->name('弹窗设置');

    Route::get('/admin/user/index', 'UserController@index')->name('用户列表');
    Route::get('/admin/user/show', 'UserController@show')->name('用户详情');
    Route::post('/admin/user/create', 'UserController@create')->name('用户创建');
    Route::post('/admin/user/update', 'UserController@update')->name('用户更新');
    Route::post('/admin/user/delete', 'UserController@delete')->name('用户删除');
    Route::post('/admin/user/merchant/modify', 'UserController@merchantModify')->name('用户商家更新');
    Route::post('/admin/user/update_auth', 'UserController@updateAuth')->name('用户认证更新');
    Route::post('/admin/user/update_api_key', 'UserController@updateApiKey')->name('用户秘钥key更新');
    Route::post('/admin/user/update_api_secret', 'UserController@updateApiSecret')->name('用户秘钥secret更新');
//    Route::post('/admin/user/add_deposit', 'UserController@addDeposit')->name('用户保证金增加');
//    Route::post('/admin/user/sub_deposit', 'UserController@subDeposit')->name('用户保证金减掉');

    Route::get('/admin/user_auth_bill/index', 'UserAuthBillController@index')->name('用户认证列表');
    Route::post('/admin/user_auth_bill/check', 'UserAuthBillController@check')->name('用户认证批准');
    Route::post('/admin/user_auth_bill/reject', 'UserAuthBillController@reject')->name('用户认证拒绝');

    Route::get('/admin/user_remark/index', 'UserRemarkController@index')->name('用户备注列表');
    Route::post('/admin/user_remark/create', 'UserRemarkController@create')->name('用户备注增加');

    Route::get('/admin/account/index', 'AccountController@index')->name('账号信息列表');
    Route::get('/admin/account/show', 'AccountController@show')->name('账号信息详情');
//    Route::post('/admin/account/create', 'AccountController@create')->name('账号信息创建');
//    Route::post('/admin/account/update', 'AccountController@update')->name('账号信息更新');
//    Route::post('/admin/account/delete', 'AccountController@delete')->name('账号信息删除');

    Route::get('/admin/account_report/index', 'AccountReportController@index')->name('举报信息列表');
    Route::get('/admin/account_report/show', 'AccountReportController@show')->name('举报信息详情');
//    Route::post('/admin/account_report/create', 'AccountReportController@create')->name('举报信息创建');
    Route::post('/admin/account_report/update', 'AccountReportController@update')->name('举报信息更新');
    Route::post('/admin/account_report/delete', 'AccountReportController@delete')->name('举报信息删除');

    Route::get('/admin/article/index', 'ArticleController@index')->name('文章列表');
    Route::get('/admin/article/show', 'ArticleController@show')->name('文章详情');
    Route::post('/admin/article/create', 'ArticleController@create')->name('文章创建');
    Route::post('/admin/article/update', 'ArticleController@update')->name('文章更新');
    Route::post('/admin/article/delete', 'ArticleController@delete')->name('文章删除');

    Route::get('/admin/message/index', 'MessageController@index')->name('消息列表');
    Route::post('/admin/message/create', 'MessageController@create')->name('消息创建');
    Route::post('/admin/message/delete', 'MessageController@delete')->name('消息删除');

    Route::get('/admin/recharge/index', 'RechargeController@index')->name('充值列表');
    Route::post('/admin/recharge/create', 'RechargeController@create')->name('充值人工操作');

    Route::get('/admin/amount_bill/index', 'AmountBillController@index')->name('积分记录列表');

    Route::get('/admin/search_bill/index', 'SearchBillController@index')->name('查询汇总列表');

    Route::get('/admin/wechat/get_server', 'WechatController@getServer')->name('微信配置详情');
    Route::post('/admin/wechat/set_server', 'WechatController@setServer')->name('微信配置更新');
    Route::post('/admin/wechat/refresh_token', 'WechatController@refreshToken')->name('微信token刷新');
    Route::get('/admin/wechat/get_menu', 'WechatController@getMenu')->name('微信菜单详情');
    Route::post('/admin/wechat/set_menu', 'WechatController@setMenu')->name('微信菜单更新');

    Route::get('/admin/vbot_job/index', 'VbotJobController@index')->name('微信清粉列表');
    Route::get('/admin/excel/index', 'ExcelController@index')->name('保存EXCEL列表');

    Route::get('/admin/product/index', 'ProductController@index')->name('产品列表');
    Route::get('/admin/user_product/index', 'UserProductController@index')->name('用户产品列表');
    Route::get('/admin/product_bill/index', 'ProductBillController@index')->name('产品订单列表');
});

//>>>>>wechat serv
Route::any('/wechat', 'WechatController@serve');

//>>>>>user_api
Route::middleware(['user.api'])->group(function () {
    Route::get('/user_api/account-report/search', 'UserApi\AccountReportController@search');
});