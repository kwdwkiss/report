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
    Route::get('/admin', 'Admin\AdminController@index');
    Route::get('/admin/logout', 'Admin\AdminController@logout');
    Route::post('/admin/login', 'Admin\AdminController@login');
});

Route::middleware(['auth:admin', 'csrf'])->group(function () {
    Route::get('/admin/list', 'Admin\AdminController@list');
    Route::post('/admin/create', 'Admin\AdminController@create');
    Route::post('/admin/update', 'Admin\AdminController@update');
    Route::post('/admin/delete', 'Admin\AdminController@delete');
    Route::get('/admin/info', 'Admin\AdminController@info');
    Route::post('/admin/modify-password', 'Admin\AdminController@modifyPassword');
});