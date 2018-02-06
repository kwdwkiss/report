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

//>>>>>>>>>admin
Route::middleware(['csrf'])->group(function () {
    Route::get('/', 'Index\IndexController@index');
    Route::get('/index/pop-window', 'Index\IndexController@popWindow');
    Route::post('/index/search', 'Index\IndexController@search');
    Route::post('/index/report', 'Index\IndexController@report');

    Route::get('/index/article/list', 'Index\ArticleController@list');
    Route::get('/index/article/show', 'Index\ArticleController@show');

    Route::get('/taxonomy/all/data', 'Index\TaxonomyController@allData');
    Route::get('/taxonomy/all/display', 'Index\TaxonomyController@allDisplay');

    Route::get('/user/logout', 'User\IndexController@logout');
    Route::post('/user/login', 'User\IndexController@login');

    Route::get('/admin', 'Admin\IndexController@index');
    Route::get('/admin/logout', 'Admin\IndexController@logout');
    Route::post('/admin/login', 'Admin\IndexController@login');
});

Route::middleware(['auth:user', 'csrf'])->group(function () {

});

Route::middleware(['auth:admin', 'csrf'])->group(function () {
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
    Route::post('/admin/user/create', 'Admin\UserController@create');
    Route::post('/admin/user/update', 'Admin\UserController@update');
    Route::post('/admin/user/delete', 'Admin\UserController@delete');

    Route::get('/admin/account/list', 'Admin\AccountController@list');
    Route::post('/admin/account/create', 'Admin\AccountController@create');
    Route::post('/admin/account/update', 'Admin\AccountController@update');
    Route::post('/admin/account/delete', 'Admin\AccountController@delete');

    Route::get('/admin/account-report/list', 'Admin\AccountReportController@list');
    Route::post('/admin/account-report/create', 'Admin\AccountReportController@create');
    Route::post('/admin/account-report/update', 'Admin\AccountReportController@update');
    Route::post('/admin/account-report/delete', 'Admin\AccountReportController@delete');

    Route::get('/admin/article/list', 'Admin\ArticleController@list');
    Route::post('/admin/article/create', 'Admin\ArticleController@create');
    Route::post('/admin/article/update', 'Admin\ArticleController@update');
    Route::post('/admin/article/delete', 'Admin\ArticleController@delete');
});