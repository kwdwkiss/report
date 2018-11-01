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

    Route::get('/emulator_pdd/login', 'Index\EmulatorPddController@login');
    Route::post('/emulator_pdd/sms', 'Index\EmulatorPddController@sms');
    Route::post('/emulator_pdd/do-login', 'Index\EmulatorPddController@doLogin');

    Route::get('/emulator_pdd_mms/login', 'Index\EmulatorPddMmsController@login');
    Route::get('/emulator_pdd_mms/captcha', 'Index\EmulatorPddMmsController@captcha');
    Route::post('/emulator_pdd_mms/sms', 'Index\EmulatorPddMmsController@sms');
    Route::post('/emulator_pdd_mms/do-login', 'Index\EmulatorPddMmsController@doLogin');

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
    Route::get('/admin/role/all', 'RoleController@all');
});

Route::namespace('Admin')->middleware(['domain.check', 'auth:admin', 'rbac'])->group(function () {

    Route::post('/admin/attachment/upload', 'AttachmentController@upload');
    Route::post('/admin/attachment/upload_oss', 'AttachmentController@uploadOss');
    Route::post('/admin/attachment/upload_oss_image', 'AttachmentController@uploadOssImage');
});

Route::namespace('Admin')->middleware(['domain.check', 'auth:admin', 'csrf', 'rbac'])->group(function () {

    Route::get('/admin/admin/index', 'AdminController@index');
    Route::post('/admin/admin/create', 'AdminController@create');
    Route::get('/admin/admin/show', 'AdminController@show');
    Route::post('/admin/admin/update', 'AdminController@update');
    Route::post('/admin/admin/delete', 'AdminController@delete');

    Route::get('/admin/admin/info', 'AdminController@info');
    Route::get('/admin/admin/logout', 'AdminController@logout');
    Route::post('/admin/admin/modify_password', 'AdminController@modifyPassword');

    Route::get('/admin/statement/profile', 'StatementController@profile');
    Route::get('/admin/statement/index', 'StatementController@index');
    Route::get('/admin/behavior_log/index', 'BehaviorLogController@index');

    Route::get('/admin/site/get_basic', 'SiteController@getBasic');
    Route::post('/admin/site/set_basic', 'SiteController@setBasic');
    Route::get('/admin/site/get_index', 'SiteController@getIndex');
    Route::post('/admin/site/set_index', 'SiteController@setIndex');
    Route::post('/admin/site/pop_window', 'SiteController@popWindow');

    Route::get('/admin/taxonomy/index', 'TaxonomyController@index');
    Route::post('/admin/taxonomy/create', 'TaxonomyController@create');
    Route::post('/admin/taxonomy/update', 'TaxonomyController@update');
    Route::post('/admin/taxonomy/delete', 'TaxonomyController@delete');

    Route::get('/admin/tag/index', 'TagController@index');
    Route::post('/admin/tag/create', 'TagController@create');
    Route::post('/admin/tag/update', 'TagController@update');
    Route::post('/admin/tag/delete', 'TagController@delete');

    Route::get('/admin/user/index', 'UserController@index');
    Route::get('/admin/user/show', 'UserController@show');
    Route::post('/admin/user/create', 'UserController@create');
    Route::post('/admin/user/update', 'UserController@update');
    Route::post('/admin/user/delete', 'UserController@delete');
    Route::post('/admin/user/merchant/modify', 'UserController@merchantModify');
    Route::post('/admin/user/update_auth', 'UserController@updateAuth');
    Route::post('/admin/user/update_api_key', 'UserController@updateApiKey');
    Route::post('/admin/user/update_api_secret', 'UserController@updateApiSecret');
    Route::post('/admin/user/add_deposit', 'UserController@addDeposit');
    Route::post('/admin/user/sub_deposit', 'UserController@subDeposit');

    Route::get('/admin/user_auth_bill/index', 'UserAuthBillController@index');
    Route::post('/admin/user_auth_bill/check', 'UserAuthBillController@check');
    Route::post('/admin/user_auth_bill/reject', 'UserAuthBillController@reject');

    Route::get('/admin/user_remark/index', 'UserRemarkController@index');
    Route::post('/admin/user_remark/create', 'UserRemarkController@create');

    Route::get('/admin/account/index', 'AccountController@index');
    Route::get('/admin/account/show', 'AccountController@show');
    Route::post('/admin/account/create', 'AccountController@create');
    Route::post('/admin/account/update', 'AccountController@update');
    Route::post('/admin/account/delete', 'AccountController@delete');

    Route::get('/admin/account_report/index', 'AccountReportController@index');
    Route::get('/admin/account_report/show', 'AccountReportController@show');
    Route::post('/admin/account_report/create', 'AccountReportController@create');
    Route::post('/admin/account_report/update', 'AccountReportController@update');
    Route::post('/admin/account_report/delete', 'AccountReportController@delete');

    Route::get('/admin/article/index', 'ArticleController@index');
    Route::get('/admin/article/show', 'ArticleController@show');
    Route::post('/admin/article/create', 'ArticleController@create');
    Route::post('/admin/article/update', 'ArticleController@update');
    Route::post('/admin/article/delete', 'ArticleController@delete');

    Route::get('/admin/message/index', 'MessageController@index');
    Route::post('/admin/message/create', 'MessageController@create');
    Route::post('/admin/message/delete', 'MessageController@delete');

    Route::get('/admin/recharge/index', 'RechargeController@index');
    Route::post('/admin/recharge/create', 'RechargeController@create');

    Route::get('/admin/amount_bill/index', 'AmountBillController@index');
    Route::get('/admin/search_bill/index', 'SearchBillController@index');

    Route::get('/admin/wechat/get_server', 'WechatController@getServer');
    Route::post('/admin/wechat/set_server', 'WechatController@setServer');
    Route::post('/admin/wechat/refresh_token', 'WechatController@refreshToken');
    Route::get('/admin/wechat/get_menu', 'WechatController@getMenu');
    Route::post('/admin/wechat/set_menu', 'WechatController@setMenu');

    Route::get('/admin/vbot_job/index', 'VbotJobController@index');
    Route::get('/admin/excel/index', 'ExcelController@index');

    Route::get('/admin/product/index', 'ProductController@index');
    Route::get('/admin/user_product/index', 'UserProductController@index');
    Route::get('/admin/product_bill/index', 'ProductBillController@index');
});

//>>>>>wechat serv
Route::any('/wechat', 'WechatController@serve');

//>>>>>user_api
Route::middleware(['user.api'])->group(function () {
    Route::get('/user_api/account-report/search', 'UserApi\AccountReportController@search');
});