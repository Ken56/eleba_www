<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
//短信验证接口
Route::get('/sms','ApiController@sendSms');
Route::post('/register','ApiController@register');
//验证登录
Route::post('/loginCheck','ApiController@loginCheck');
//用户修改密码changePassword
Route::post('/changePassword','ApiController@changePassword');
//忘记密码forgetPassword
Route::post('/forgetPassword','ApiController@forgetPassword');
//地址列表接口addressList
Route::get('/addressList','ApiController@addressList');
//指定地址接口address
Route::post('/addAddress','ApiController@addAddress');
// 指定地址接口
Route::get('/address','ApiController@address');
// 保存修改地址接口
Route::post('/editAddress','ApiController@editAddress');
// 保存购物车接口
Route::post('/addCart','ApiController@addCart');
// 保存购物车接口
Route::post('/addCart','ApiController@addCart');
//获取购物车数据接口
Route::get('/cart','ApiController@cart');
//添加订单接口
Route::post('/addorder','ApiController@addorder');
//添加订单接口order
Route::get('/order','ApiController@order');
// 获得订单列表接口
Route::get('/orderList','ApiController@orderList');

