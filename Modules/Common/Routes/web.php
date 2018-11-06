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

Route::prefix('common')->group(function() {


});

//>>>>>user_api
Route::middleware(['user.api'])->group(function () {
    Route::get('/user_api/account-report/search', 'UserApi\AccountReportController@search');
});

//>>>>>wechat serv
Route::any('/wechat', 'Wechat\WechatController@serve');
