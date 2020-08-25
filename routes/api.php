<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::group(['namespace' => 'Api'],function()
{
    Route::get('/countries','SettingController@get_countries');
    Route::get('/abouts','SettingController@get_abouts');
    Route::get('/terms','SettingController@get_terms');

    Route::group(['prefix' => 'auth'],function()
    {
        Route::post('/update_token','AuthController@update_token');
        Route::post('/register','AuthController@register');
        Route::post('/login','AuthController@login');
        Route::post('/update','AuthController@update');
        Route::post('/update_password','AuthController@update_password');
        Route::post('/city/update','AuthController@update_city');
        Route::post('/send_reset','AuthController@send_reset');
        Route::post('/check_reset','AuthController@check_reset');
        Route::post('/password_reset','AuthController@password_reset');
        Route::post('/addresses','AuthController@get_addresses');
        Route::post('/address/store','AuthController@store_address');
        Route::post('/address/update','AuthController@update_address');
        Route::post('/address/delete','AuthController@destroy_address');
        Route::post('/contact_us','AuthController@contact_us');
        Route::post('/search_request','AuthController@search_request');
        Route::post('/notifications','AuthController@notifications');
        Route::post('/notification/delete','AuthController@destroy_notification');
        Route::post('/user/profile','AuthController@profile');
    });

    Route::post('/home','HomeController@index');
    Route::post('/search','HomeController@search');
    Route::post('/categories','HomeController@categories');
    Route::post('/category/products','HomeController@category_products');
    Route::post('/all_products','HomeController@all_products');
    Route::post('/packs','PackController@index');
    Route::post('/pack/subscribe','PackController@subscribe');

    Route::post('/favorites','FavoriteController@index');
    Route::post('/favorite/handle','FavoriteController@handle');

    Route::post('/product/show','ProductController@show');

    Route::post('/cart/index','CartController@index');
    Route::post('/cart/store','CartController@store');
    Route::post('/cart/delete','CartController@destroy');
    Route::post('/cart/checkout','CartController@checkout');

    Route::post('/orders/index','OrderController@index');
    Route::post('/order/show','OrderController@show');
    Route::post('/order/update','OrderController@update');
    Route::post('/order/change_status','OrderController@change_status');

    Route::post('/chat','ChatController@index');
    Route::post('/chat/room','ChatController@show');
    Route::post('/chat/store','ChatController@store');


});
