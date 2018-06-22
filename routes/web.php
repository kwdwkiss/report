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

Route::middleware(['csrf'])->group(function () {
    Route::get('/check_tb', 'Index\CheckAccountController@tb');
    Route::get('/check_pdd', 'Index\CheckAccountController@pdd');
    Route::get('/check_jd', 'Index\CheckAccountController@jd');

    Route::get('/emulator_pdd/login', 'Index\EmulatorPddController@login');
    Route::post('/emulator_pdd/sms', 'Index\EmulatorPddController@sms');
    Route::post('/emulator_pdd/do-login', 'Index\EmulatorPddController@doLogin');

    Route::get('/emulator_pdd_mms/login', 'Index\EmulatorPddMmsController@login');
    Route::get('/emulator_pdd_mms/captcha', 'Index\EmulatorPddMmsController@captcha');
    Route::post('/emulator_pdd_mms/sms', 'Index\EmulatorPddMmsController@sms');
    Route::post('/emulator_pdd_mms/do-login', 'Index\EmulatorPddMmsController@doLogin');

    Route::get('/', 'Index\IndexController@index')->name('login');
    Route::get('/index/pop-window', 'Index\IndexController@popWindow');
    Route::post('/index/upload-oss', 'Index\IndexController@uploadOss');
    Route::post('/index/behavior-log', 'Index\IndexController@behaviorLog');
    Route::post('/index/one-key-excel', 'Index\IndexController@oneKeyExcel');

    Route::get('/index/article/list', 'Index\ArticleController@list');
    Route::get('/index/article/show', 'Index\ArticleController@show');

    Route::get('/taxonomy/all/data', 'Index\TaxonomyController@allData');
    Route::get('/taxonomy/all/display', 'Index\TaxonomyController@allDisplay');

    Route::get('/user/logout', 'User\UserController@logout');
    Route::post('/user/login', 'User\UserController@login');
    Route::post('/user/forget-password', 'User\UserController@forgetPassword');
    Route::post('/user/register', 'User\UserController@register');
    Route::post('/user/sms', 'User\UserController@sms');

    Route::get('/user/recharge/callback', 'User\RechargeController@recharge');

    Route::get('/admin', 'Admin\IndexController@index');
    Route::get('/admin/logout', 'Admin\IndexController@logout');
    Route::post('/admin/login', 'Admin\IndexController@login');
});

Route::middleware(['auth:user', 'csrf'])->group(function () {
    Route::get('/user/info', 'User\UserController@info');
    Route::post('/index/search', 'Index\IndexController@search');
    Route::post('/index/report', 'Index\IndexController@report');
    Route::get('/user/recharge/index', 'User\RechargeController@index');
    Route::get('/user/amount/index', 'User\AmountController@index');

    Route::get('/user/notification', 'User\NotificationController@notificationList');
    Route::get('/user/unread-notification', 'User\NotificationController@unreadNotificationList');
    Route::get('/user/unread-notification/count', 'User\NotificationController@unreadNotificationCount');
    Route::post('/user/read-notification', 'User\NotificationController@readNotification');

    Route::post('/user/modify', 'User\UserController@modify');
    Route::post('/user/merchant/modify', 'User\UserController@merchantModify');

    Route::get('/user/report/index', 'User\AccountReportController@index');
    Route::post('/user/report/hide', 'User\AccountReportController@hide');

    Route::post('/mobile/search', 'Index\MobileController@search');
});

//>>>>>>>>>admin
Route::middleware(['auth:admin'])->group(function () {
    Route::post('/admin/upload', 'Admin\IndexController@upload');
    Route::post('/admin/upload-oss', 'Admin\IndexController@uploadOss');
});

Route::middleware(['auth:admin', 'csrf'])->group(function () {
    Route::get('/admin/dashboard', 'Admin\IndexController@statement');
    Route::get('/admin/statement/list', 'Admin\StatementController@index');

    Route::get('/admin/info', 'Admin\IndexController@info');
    Route::post('/admin/modify-password', 'Admin\IndexController@modifyPassword');

    Route::get('/admin/list', 'Admin\AdminController@list');
    Route::post('/admin/create', 'Admin\AdminController@create');
    Route::post('/admin/update', 'Admin\AdminController@update');
    Route::post('/admin/delete', 'Admin\AdminController@delete');

    Route::get('/admin/site/basic', 'Admin\SiteController@getBasic');
    Route::post('/admin/site/basic', 'Admin\SiteController@setBasic');
    Route::get('/admin/site/index', 'Admin\SiteController@getIndex');
    Route::post('/admin/site/index', 'Admin\SiteController@setIndex');
    Route::post('/admin/site/pop-window', 'Admin\SiteController@popWindow');

    Route::get('/admin/taxonomy/list', 'Admin\TaxonomyController@list');
    Route::post('/admin/taxonomy/create', 'Admin\TaxonomyController@create');
    Route::post('/admin/taxonomy/update', 'Admin\TaxonomyController@update');
    Route::post('/admin/taxonomy/delete', 'Admin\TaxonomyController@delete');

    Route::get('/admin/tag/list', 'Admin\TagController@list');
    Route::post('/admin/tag/create', 'Admin\TagController@create');
    Route::post('/admin/tag/update', 'Admin\TagController@update');
    Route::post('/admin/tag/delete', 'Admin\TagController@delete');

    Route::get('/admin/user/list', 'Admin\UserController@list');
    Route::get('/admin/user/show', 'Admin\UserController@show');
    Route::post('/admin/user/create', 'Admin\UserController@create');
    Route::post('/admin/user/update', 'Admin\UserController@update');
    Route::post('/admin/user/delete', 'Admin\UserController@delete');
    Route::post('/admin/user/merchant/modify', 'Admin\UserController@merchantModify');
    Route::post('/admin/user/update-auth', 'Admin\UserController@updateAuth');
    Route::post('/admin/user/update-api-key', 'Admin\UserController@updateApiKey');
    Route::post('/admin/user/update-api-secret', 'Admin\UserController@updateApiSecret');

    Route::get('/admin/account/list', 'Admin\AccountController@list');
    Route::get('/admin/account/show', 'Admin\AccountController@show');
    Route::post('/admin/account/create', 'Admin\AccountController@create');
    Route::post('/admin/account/update', 'Admin\AccountController@update');
    Route::post('/admin/account/delete', 'Admin\AccountController@delete');

    Route::get('/admin/account-report/list', 'Admin\AccountReportController@list');
    Route::get('/admin/account-report/show', 'Admin\AccountReportController@show');
    Route::post('/admin/account-report/create', 'Admin\AccountReportController@create');
    Route::post('/admin/account-report/update', 'Admin\AccountReportController@update');
    Route::post('/admin/account-report/delete', 'Admin\AccountReportController@delete');

    Route::get('/admin/article/list', 'Admin\ArticleController@list');
    Route::get('/admin/article/show', 'Admin\ArticleController@show');
    Route::post('/admin/article/create', 'Admin\ArticleController@create');
    Route::post('/admin/article/update', 'Admin\ArticleController@update');
    Route::post('/admin/article/delete', 'Admin\ArticleController@delete');

    Route::get('/admin/message/list', 'Admin\MessageController@list');
    Route::post('/admin/message/create', 'Admin\MessageController@create');
    Route::post('/admin/message/delete', 'Admin\MessageController@delete');

    Route::get('/admin/recharge/list', 'Admin\RechargeController@list');
    Route::post('/admin/recharge/create', 'Admin\RechargeController@create');

    Route::get('/admin/amount-bill/list', 'Admin\AmountController@index');
    Route::get('/admin/search-bill/list', 'Admin\SearchBillController@index');

    Route::get('/admin/wechat/get-server', 'Admin\WechatController@getServer');
    Route::post('/admin/wechat/set-server', 'Admin\WechatController@setServer');
    Route::post('/admin/wechat/refresh-token', 'Admin\WechatController@refreshToken');
    Route::get('/admin/wechat/get-menu', 'Admin\WechatController@getMenu');
    Route::post('/admin/wechat/set-menu', 'Admin\WechatController@setMenu');
});

//>>>>>wechat serv
Route::any('/wechat', 'WechatController@serve');

//>>>>>user_api
Route::middleware(['user.api'])->group(function () {
    Route::get('/user_api/account-report/search', 'UserApi\AccountReportController@search');
});