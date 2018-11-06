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

    Route::get('/check_tb', 'CheckAccountController@tb');
    Route::get('/check_pdd', 'CheckAccountController@pdd');
    Route::get('/check_jd', 'CheckAccountController@jd');
    Route::get('/check_geo', 'CheckAccountController@geo');

    Route::get('/', 'IndexController@index')->name('login');
    Route::get('/index/pop-window', 'IndexController@popWindow');
    Route::get('/index/index/recharge_url', 'IndexController@rechargeUrl');
    Route::post('/index/upload-oss', 'IndexController@uploadOss');
    Route::post('/index/behavior-log', 'IndexController@behaviorLog');
    Route::post('/index/one-key-excel', 'IndexController@oneKeyExcel');
    Route::get('/index/excel_cost_type', 'IndexController@excelCostType');

    Route::get('/index/article/list', 'ArticleController@list');
    Route::get('/index/article/show', 'ArticleController@show');

    Route::get('/index/index/basic', 'IndexController@basic');

    Route::get('/taxonomy/all/data', 'TaxonomyController@allData');
    Route::get('/taxonomy/all/display', 'TaxonomyController@allDisplay');

    Route::get('/index/product/index', 'ProductController@index');
    Route::get('/index/product/show', 'ProductController@show');

    Route::get('/user/logout', 'UserController@logout');
    Route::post('/user/login', 'UserController@login');
    Route::post('/user/forget-password', 'UserController@forgetPassword');
    Route::post('/user/register', 'UserController@register');
    Route::post('/user/sms', 'UserController@sms');

    Route::get('/user/recharge/callback', 'RechargeController@apiCallback');
    Route::get('/user/recharge/page-callback', 'RechargeController@pageCallback');
});

Route::middleware(['auth:user', 'csrf'])->group(function () {

    Route::get('/user/info', 'UserController@info');
    Route::post('/index/search', 'IndexController@search');
    Route::post('/index/report', 'IndexController@report');
    Route::get('/user/recharge/index', 'RechargeController@index');
    Route::get('/user/amount/index', 'AmountController@index');

    Route::get('/index/user_product/index', 'UserProductController@index');
    Route::post('/index/user_product/create', 'UserProductController@create');

    Route::get('/user/notification', 'NotificationController@notificationList');
    Route::get('/user/unread-notification', 'NotificationController@unreadNotificationList');
    Route::get('/user/unread-notification/count', 'NotificationController@unreadNotificationCount');
    Route::post('/user/read-notification', 'NotificationController@readNotification');

    Route::post('/user/modify', 'UserController@modify');
    Route::post('/user/merchant/modify', 'UserController@merchantModify');

    Route::get('/user/report/index', 'AccountReportController@index');
    Route::post('/user/report/hide', 'AccountReportController@hide');

    Route::get('/user/account_favor/index', 'AccountFavorController@index');
    Route::post('/user/account_favor/create', 'AccountFavorController@create');

    Route::get('/user/excel/index', 'ExcelController@index');
    Route::post('/user/excel/create', 'ExcelController@create');
    Route::post('/user/excel/update', 'ExcelController@update');
    Route::post('/user/excel/delete', 'ExcelController@delete');

    Route::get('/user/user-auth-bill/index', 'UserAuthBillController@index');
    Route::post('/user/user-auth-bill/apply', 'UserAuthBillController@apply');

    Route::get('/user/vbot/index', 'VbotController@index');
    Route::post('/user/vbot/create', 'VbotController@create');
    Route::get('/user/vbot/detail', 'VbotController@detail');
    Route::post('/user/vbot/delete', 'VbotController@delete');
    Route::get('/user/vbot/status', 'VbotController@status');
    Route::post('/user/vbot/run', 'VbotController@run');
    Route::post('/user/vbot/stop', 'VbotController@stop');
    Route::post('/user/vbot/send', 'VbotController@send');
    Route::post('/user/vbot/add_send', 'VbotController@addSend');
    Route::post('/user/vbot/delete_send', 'VbotController@deleteSend');

    Route::post('/mobile/search', 'MobileController@search');
});
