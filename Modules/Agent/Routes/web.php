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

Route::prefix('agent')->middleware(['csrf'])->group(function () {

    Route::get('/', 'IndexController@index');
    Route::post('/agent/login', 'AgentController@login');
    Route::get('/agent/logout', 'AgentController@logout');
});

Route::prefix('agent')->middleware(['auth:agent', 'csrf'])->group(function () {

    Route::get('/agent/info', 'AgentController@info');
    Route::post('/agent/modify_password', 'AgentController@modifyPassword');

    //下面路由未来可能rbac

    Route::get('/user/index', 'UserController@index')->name('用户列表');
    Route::get('/user/show', 'UserController@show')->name('用户详情');
    Route::post('/user/create', 'UserController@create')->name('用户创建');
    Route::post('/user/update', 'UserController@update')->name('用户更新');
    Route::post('/user/delete', 'UserController@delete')->name('用户删除');
    Route::post('/user/enable_report', 'UserController@enableReport')->name('用户删除');

    Route::get('/account_report/index', 'AccountReportController@index')->name('举报信息列表');
    Route::get('/account_report/show', 'AccountReportController@show')->name('举报信息详情');
});
