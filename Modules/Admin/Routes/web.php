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

Route::prefix('admin')->middleware(['domain.check', 'csrf'])->group(function () {

    Route::get('/', 'IndexController@index');
    Route::post('/admin/login', 'AdminController@login');
    Route::get('/admin/logout', 'AdminController@logout');
    Route::get('/role/all', 'RoleController@all');
    Route::get('/permission/all', 'PermissionController@all');
});

Route::prefix('admin')->middleware(['domain.check', 'auth:admin', 'rbac'])->group(function () {

    Route::post('/attachment/upload', 'AttachmentController@upload')->name('上传附件到本地');
    Route::post('/attachment/upload_oss', 'AttachmentController@uploadOss')->name('上传附件到oss');
    Route::post('/attachment/upload_oss_image', 'AttachmentController@uploadOssImage')->name('上传身份证到oss加水印');
});

Route::prefix('admin')->middleware(['domain.check', 'auth:admin', 'csrf'])->group(function () {

    Route::get('/admin/info', 'AdminController@info');
    Route::post('/admin/modify_password', 'AdminController@modifyPassword');
});

Route::prefix('admin')->middleware(['domain.check', 'auth:admin', 'csrf', 'rbac'])->group(function () {

    Route::get('/admin/index', 'AdminController@index')->name('管理员列表');
    Route::post('/admin/create', 'AdminController@create')->name('管理员创建');
    Route::get('/admin/show', 'AdminController@show')->name('管理员详情');
    Route::post('/admin/update', 'AdminController@update')->name('管理员更新');
    Route::post('/admin/delete', 'AdminController@delete')->name('管理员删除');

//    Route::get('/admin/permission/index', 'PermissionController@index')->name('权限列表');
    Route::post('/permission/refresh', 'PermissionController@refresh')->name('权限刷新');

    Route::get('/role/index', 'RoleController@index')->name('角色列表');
    Route::get('/role/show', 'RoleController@show')->name('角色详情');
    Route::post('/role/update', 'RoleController@update')->name('角色更新');

    Route::get('/taxonomy/index', 'TaxonomyController@index')->name('分类列表');
    Route::post('/taxonomy/create', 'TaxonomyController@create')->name('分类创建');
    Route::post('/taxonomy/update', 'TaxonomyController@update')->name('分类更新');
    Route::post('/taxonomy/delete', 'TaxonomyController@delete')->name('分类删除');

//    Route::get('/tag/index', 'TagController@index')->name('标签列表');
//    Route::post('/tag/create', 'TagController@create')->name('标签创建');
//    Route::post('/tag/update', 'TagController@update')->name('标签更新');
//    Route::post('/tag/delete', 'TagController@delete')->name('标签删除');

    Route::get('/statement/profile', 'StatementController@profile')->name('报表概况');
    Route::get('/statement/index', 'StatementController@index')->name('报表列表');
    Route::get('/behavior_log/index', 'BehaviorLogController@index')->name('行为日志列表');

    Route::get('/site/get_basic', 'SiteController@getBasic')->name('网站设置详情');
    Route::post('/site/set_basic', 'SiteController@setBasic')->name('网站设置更新');

    Route::get('/site/get_index', 'SiteController@getIndex')->name('首页设置详情');
    Route::post('/site/set_index', 'SiteController@setIndex')->name('首页设置更新');
    Route::post('/site/pop_window', 'SiteController@popWindow')->name('弹窗设置');

    Route::get('/user/index', 'UserController@index')->name('用户列表');
    Route::get('/user/show', 'UserController@show')->name('用户详情');
    Route::post('/user/create', 'UserController@create')->name('用户创建');
    Route::post('/user/update', 'UserController@update')->name('用户更新');
    Route::post('/user/delete', 'UserController@delete')->name('用户删除');
    Route::post('/user/merchant/modify', 'UserController@merchantModify')->name('用户商家更新');
    Route::post('/user/update_auth', 'UserController@updateAuth')->name('用户认证更新');
    Route::post('/user/update_api_key', 'UserController@updateApiKey')->name('用户秘钥key更新');
    Route::post('/user/update_api_secret', 'UserController@updateApiSecret')->name('用户秘钥secret更新');
    Route::post('/user/enable_favor', 'UserController@enableFavor')->name('用户开通点赞');
//    Route::post('/user/add_deposit', 'UserController@addDeposit')->name('用户保证金增加');
//    Route::post('/user/sub_deposit', 'UserController@subDeposit')->name('用户保证金减掉');

    Route::get('/user_auth_bill/index', 'UserAuthBillController@index')->name('用户认证列表');
    Route::post('/user_auth_bill/check', 'UserAuthBillController@check')->name('用户认证批准');
    Route::post('/user_auth_bill/reject', 'UserAuthBillController@reject')->name('用户认证拒绝');

    Route::get('/user_remark/index', 'UserRemarkController@index')->name('用户备注列表');
    Route::post('/user_remark/create', 'UserRemarkController@create')->name('用户备注增加');

    Route::get('/account/index', 'AccountController@index')->name('账号信息列表');
    Route::get('/account/show', 'AccountController@show')->name('账号信息详情');
//    Route::post('/account/create', 'AccountController@create')->name('账号信息创建');
//    Route::post('/account/update', 'AccountController@update')->name('账号信息更新');
//    Route::post('/account/delete', 'AccountController@delete')->name('账号信息删除');

    Route::get('/account_report/index', 'AccountReportController@index')->name('举报信息列表');
    Route::get('/account_report/show', 'AccountReportController@show')->name('举报信息详情');
//    Route::post('/account_report/create', 'AccountReportController@create')->name('举报信息创建');
    Route::post('/account_report/update', 'AccountReportController@update')->name('举报信息更新');
    Route::post('/account_report/delete', 'AccountReportController@delete')->name('举报信息删除');

    Route::get('/account_favor/index', 'AccountFavorController@index')->name('点赞信息列表');
//    Route::post('/account_favor/delete', 'AccountFavorController@delete')->name('点赞信息删除');

    Route::get('/article/index', 'ArticleController@index')->name('文章列表');
    Route::get('/article/show', 'ArticleController@show')->name('文章详情');
    Route::post('/article/create', 'ArticleController@create')->name('文章创建');
    Route::post('/article/update', 'ArticleController@update')->name('文章更新');
    Route::post('/article/delete', 'ArticleController@delete')->name('文章删除');

    Route::get('/admin_article/index', 'AdminArticleController@index')->name('内部文章列表');
    Route::get('/admin_article/show', 'AdminArticleController@show')->name('内部文章详情');
    Route::get('/admin_article/show_last', 'AdminArticleController@showLast')->name('内部文章最新详情');
    Route::post('/admin_article/create', 'AdminArticleController@create')->name('内部文章创建');
    Route::post('/admin_article/update', 'AdminArticleController@update')->name('内部文章更新');
    Route::post('/admin_article/delete', 'AdminArticleController@delete')->name('内部文章删除');

    Route::get('/message/index', 'MessageController@index')->name('消息列表');
    Route::post('/message/create', 'MessageController@create')->name('消息创建');
    Route::post('/message/delete', 'MessageController@delete')->name('消息删除');

    Route::get('/recharge/index', 'RechargeController@index')->name('充值列表');
    Route::post('/recharge/create', 'RechargeController@create')->name('充值人工操作');

    Route::get('/amount_bill/index', 'AmountBillController@index')->name('积分记录列表');

    Route::get('/search_bill/index', 'SearchBillController@index')->name('查询汇总列表');

    Route::get('/wechat/get_server', 'WechatController@getServer')->name('微信配置详情');
    Route::post('/wechat/set_server', 'WechatController@setServer')->name('微信配置更新');
    Route::post('/wechat/refresh_token', 'WechatController@refreshToken')->name('微信token刷新');
    Route::get('/wechat/get_menu', 'WechatController@getMenu')->name('微信菜单详情');
    Route::post('/wechat/set_menu', 'WechatController@setMenu')->name('微信菜单更新');

    Route::get('/vbot_job/index', 'VbotJobController@index')->name('微信清粉列表');
    Route::get('/excel/index', 'ExcelController@index')->name('保存EXCEL列表');

    Route::get('/product/index', 'ProductController@index')->name('产品列表');
    Route::get('/user_product/index', 'UserProductController@index')->name('用户产品列表');
    Route::get('/product_bill/index', 'ProductBillController@index')->name('产品订单列表');
});