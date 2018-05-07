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

Route::get('/', function () {
    return view('welcome');
});

//商家店铺接口
Route::get('/shops','ApiController@shops');
//店铺分类
Route::get('/shop','ApiController@shop');
//短信验证
Route::get('/sms','SmsController@sendSms');