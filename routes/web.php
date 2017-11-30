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
    Route::get('/admin', 'Admin\IndexController@index');
    Route::get('/admin/logout', 'Admin\IndexController@logout');
    Route::post('/admin/login', 'Admin\IndexController@login');
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

    Route::get('/admin/taxonomy/list', 'Admin\TaxonomyController@list');
    Route::get('/admin/taxonomy/report-type/list', 'Admin\TaxonomyController@reportTypeList');
    Route::get('/admin/taxonomy/account-type/list', 'Admin\TaxonomyController@accountTypeList');
    Route::get('/admin/taxonomy/account-status/list', 'Admin\TaxonomyController@accountStatusList');
    Route::get('/admin/taxonomy/article-type/list', 'Admin\TaxonomyController@articleTypeList');
    Route::post('/admin/taxonomy/create', 'Admin\TaxonomyController@create');
    Route::post('/admin/taxonomy/update', 'Admin\TaxonomyController@update');
    Route::post('/admin/taxonomy/delete', 'Admin\TaxonomyController@delete');

    Route::get('/admin/tag/list', 'Admin\TagController@list');
    Route::post('/admin/tag/create', 'Admin\TagController@create');
    Route::post('/admin/tag/update', 'Admin\TagController@update');
    Route::post('/admin/tag/delete', 'Admin\TagController@delete');

    Route::get('/admin/user-profile/list', 'Admin\UserProfileController@list');
    Route::post('/admin/user-profile/create', 'Admin\UserProfileController@create');
    Route::post('/admin/user-profile/update', 'Admin\UserProfileController@update');
    Route::post('/admin/user-profile/delete', 'Admin\UserProfileController@delete');

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