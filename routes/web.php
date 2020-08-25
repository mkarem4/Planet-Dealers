<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Admin;
use App\Http\Middleware\Web;
use App\Http\Middleware\User;
use App\Http\Middleware\Seller;
use App\Http\Middleware\Buyer;


Route::post('/{user}/change_language','LanguageController@change');

Route::group(['namespace' => 'Web','middleware' => Web::class],function()
{
    Route::get('/login','AuthController@login_view');
    Route::post('/login','AuthController@login');

    Route::get('/register','AuthController@register_view');
    Route::post('/register','AuthController@register');

    Route::get('/password/reset/view','AuthController@reset_view');
    Route::post('/password/code/send','AuthController@send_reset');
    Route::post('/password/reset','AuthController@password_reset');

    Route::get('/','HomeController@index');
    Route::get('/products','HomeController@products');
    Route::get('/product/{id}/details','ProductController@show');
    Route::get('/seller/{id}/profile','ProductController@seller_profile');

    Route::get('/change_country/{id}','HomeController@change_country');

    Route::get('/about_us','HomeController@about_us');
    Route::get('/terms','HomeController@terms');
    Route::post('/newsletter/subscribe','HomeController@news_subscribe');

    Route::get('/contact_us','HomeController@contact_view');
    Route::post('/contact_us','HomeController@contact');

    Route::get('/search_request','HomeController@search_request_view');
    Route::post('/search_request','HomeController@search_request');

    Route::get('/packs','PackController@index');

    Route::get('/compare','CompareController@index');
    Route::post('/ajax/compare_handle','CompareController@handle');


    Route::group(['middleware' => User::class], function()
    {
        Route::get('/profile','AuthController@profile');
        Route::post('/profile/update','AuthController@update');
        Route::post('/profile/password/update','AuthController@update_password');

        Route::get('/wishlist','WishlistController@index');
        Route::get('/notifications','AuthController@notifications');

        Route::get('/cart','CartController@index');
        Route::post('/cart/store','CartController@store');
        Route::post('/cart/update','CartController@update');
        Route::post('/cart/checkout','CartController@checkout');

        Route::get('/profile/orders','AuthController@orders');

        Route::post('/pack/subscribe', 'PackController@subscribe');

        Route::post('/product/rate','ProductController@rate');

        Route::get('/logout','AuthController@logout');

        Route::group(['prefix' => 'profile',], function()
        {
            Route::group(['middleware' => Seller::class], function()
            {
                Route::get('/products', 'ProductController@index');
                Route::get('/product/create', 'ProductController@create');
                Route::post('/product/store', 'ProductController@store');
                Route::get('/product/{id}/edit','ProductController@edit');
                Route::post('/product/update', 'ProductController@update');
                Route::post('/product/change_status', 'ProductController@change_status');
                Route::post('/product/delete', 'ProductController@destroy');
            });

            Route::get('/orders','OrderController@index');
            Route::get('/order/{id}/details','OrderController@show');
            Route::post('/order/update','OrderController@update');
            Route::post('/order/change_status','OrderController@change_status');

            Route::group(['middleware' => Buyer::class], function()
            {
                Route::get('/addresses', 'AddressController@index');
                Route::post('/address/store', 'AddressController@store');
                Route::post('/address/update', 'AddressController@update');
                Route::post('/address/delete', 'AddressController@destroy');
            });

            Route::get('/inbox','MessageController@inbox');
            Route::post('/message/store','MessageController@store');
            Route::post('/message/store_profile','MessageController@store_profile');
            Route::post('/message/fetch','MessageController@fetch');
        });

        Route::group(['prefix' => 'ajax'], function()
        {
            Route::post('/get_variations_options','ProductController@get_variations_options');
            Route::post('/wishlist_handle','WishlistController@handle');
            Route::post('/cart_remove','CartController@destroy');
        });
    });
});

Route::group(['prefix' => '/admin','namespace' => 'Admin'],function()
{
    Route::get('/login','AuthController@view');
    Route::post('/login','AuthController@login');

    Route::group(['middleware' => Admin::class], function()
    {
        Route::get('/dashboard', 'HomeController@index');
        Route::get('/days_orders_graph', 'HomeController@days_orders_graph');
        Route::get('/month_orders_graph', 'HomeController@month_orders_graph');

        Route::get('/logout', 'AuthController@logout');
        Route::get('/profile', 'AuthController@show');
        Route::post('/profile/update', 'AuthController@update');

        Route::get('/countries/index', 'CountryController@index');
        Route::get('/country/{parent_id}/cities', 'CountryController@subs');
        Route::get('/country/create', 'CountryController@create');
        Route::get('/country/city/create', 'CountryController@create_sub');
        Route::post('/country/store', 'CountryController@store');
        Route::post('/country/city/store', 'CountryController@store_sub');
        Route::get('/country/{id}/edit', 'CountryController@edit');
        Route::get('/country/city/{id}/edit', 'CountryController@edit_sub');
        Route::post('/country/update', 'CountryController@update');
        Route::post('/country/city/update', 'CountryController@update_sub');
        Route::post('/country/change_status', 'CountryController@change_status');
        Route::post('/country/delete', 'CountryController@destroy');

        Route::get('/admins/index', 'AdminController@index');
        Route::get('/admin/create', 'AdminController@create');
        Route::post('/admin/store', 'AdminController@store');
        Route::get('/admin/{id}/edit', 'AdminController@edit');
        Route::post('/admin/update', 'AdminController@update');
        Route::post('/admin/change_status', 'AdminController@change_status');
        Route::post('/admin/delete', 'AdminController@destroy');

        Route::get('/categories/index', 'CategoryController@index');
        Route::get('/category/{parent_id}/subs', 'CategoryController@subs');
        Route::get('/category/create', 'CategoryController@create');
        Route::get('/category/sub/create', 'CategoryController@create_sub');
        Route::get('/category/sec/create', 'CategoryController@create_sec');
        Route::post('/category/store', 'CategoryController@store');
        Route::post('/category/store/sub', 'CategoryController@store_sub');
        Route::post('/category/store/sec', 'CategoryController@store_sec');
        Route::get('/category/{id}/edit', 'CategoryController@edit');
        Route::get('/category/sub/{id}/edit', 'CategoryController@edit_sub');
        Route::get('/category/sec/{id}/edit', 'CategoryController@edit_sec');
        Route::post('/category/update', 'CategoryController@update');
        Route::post('/category/update/sub', 'CategoryController@update_sub');
        Route::post('/category/update/sec', 'CategoryController@update_sec');
        Route::post('/category/change_status', 'CategoryController@change_status');
        Route::post('/category/delete', 'CategoryController@destroy');

        Route::get('/variations/index', 'VariationController@index');
        Route::post('/variation/store', 'VariationController@store');
        Route::post('/variation/update', 'VariationController@update');
        Route::post('/variation/change_status', 'VariationController@change_status');
        Route::post('/variation/delete', 'VariationController@destroy');
        Route::get('/variation/{id}/options','VariationController@options');
        Route::post('/variation/option/store','VariationController@option_store');
        Route::post('/variation/option/update','VariationController@option_update');
        Route::post('/variation/option/change_status','VariationController@option_change_status');
        Route::post('/variation/option/delete','VariationController@option_destroy');

        Route::get('/banks/index', 'BankController@index');
        Route::get('/bank/create', 'BankController@create');
        Route::post('/bank/store', 'BankController@store');
        Route::get('/bank/{id}/edit', 'BankController@edit');
        Route::post('/bank/update', 'BankController@update');
        Route::post('/bank/change_status', 'BankController@change_status');
        Route::post('/bank/delete', 'BankController@destroy');

        Route::get('/packs/index', 'PackController@index');
        Route::get('/pack/create', 'PackController@create');
        Route::post('/pack/store', 'PackController@store');
        Route::get('/pack/{id}/edit', 'PackController@edit');
        Route::post('/pack/update', 'PackController@update');
        Route::post('/pack/change_status', 'PackController@change_status');
        Route::post('/pack/make_default', 'PackController@make_default');
        Route::post('/pack/delete', 'PackController@destroy');

        Route::get('/transfers/index', 'TransferController@index');
        Route::post('/transfer/change_status', 'TransferController@change_status');

        Route::get('/merchants/index', 'MerchantController@index');
        Route::get('/merchant/create', 'MerchantController@create');
        Route::post('/merchant/store', 'MerchantController@store');
        Route::get('/merchant/{id}/edit', 'MerchantController@edit');
        Route::post('/merchant/update', 'MerchantController@update');
        Route::post('/merchant/change_status', 'MerchantController@change_status');
        Route::post('/merchant/delete', 'MerchantController@destroy');
        Route::get('/merchants/export', 'MerchantController@export');

        Route::get('/products/index', 'ProductController@index');
        Route::post('/product/change_status', 'ProductController@change_status');
        Route::post('/product/feature_handle', 'ProductController@feature_handle');
        Route::post('/product/delete', 'ProductController@destroy');

        Route::get('/orders/index', 'OrderController@index');
        Route::get('/order/{id}/details', 'OrderController@show');
        Route::post('/order/cancel', 'OrderController@cancel');
        Route::post('/order/delete', 'OrderController@destroy');

        Route::get('/contacts/index', 'ContactController@index');
        Route::get('/contact/{id}/show', 'ContactController@show');
        Route::get('/contact/{id}/close', 'ContactController@close');
        Route::get('/contact/{id}/delete', 'ContactController@destroy');

        Route::get('/search_requests/index', 'SearchRequestController@index');
        Route::get('/search_request/{id}/show', 'SearchRequestController@show');
        Route::get('/search_request/{id}/close', 'SearchRequestController@close');
        Route::get('/search_request/{id}/delete', 'SearchRequestController@destroy');

        Route::group(['prefix' => '/settings'],function()
        {
            Route::get('/slides/index','SliderController@index');
            Route::get('/slide/create','SliderController@create');
            Route::post('/slide/store','SliderController@store');
            Route::get('/slide/{id}/edit','SliderController@edit');
            Route::post('/slide/update','SliderController@update');
            Route::post('/slide/change_status','SliderController@change_status');
            Route::post('/slide/delete','SliderController@destroy');

            Route::get('/banners/index','BannerController@index');
            Route::get('/banner/create','BannerController@create');
            Route::post('/banner/store','BannerController@store');
            Route::get('/banner/{id}/edit','BannerController@edit');
            Route::post('/banner/update','BannerController@update');
            Route::post('/banner/change_status','BannerController@change_status');
            Route::post('/banner/delete','BannerController@destroy');

            Route::get('/brands/index','BrandController@index');
            Route::get('/brand/create','BrandController@create');
            Route::post('/brand/store','BrandController@store');
            Route::get('/brand/{id}/edit','BrandController@edit');
            Route::post('/brand/update','BrandController@update');
            Route::post('/brand/change_status','BrandController@change_status');
            Route::post('/brand/delete','BrandController@destroy');

            Route::get('/socials/index','SocialController@index');
            Route::get('/social/create','SocialController@create');
            Route::post('/social/store','SocialController@store');
            Route::get('/social/{id}/edit','SocialController@edit');
            Route::post('/social/update','SocialController@update');
            Route::post('/social/change_status','SocialController@change_status');
            Route::post('/social/delete','SocialController@destroy');

            Route::get('/abouts/edit','AboutController@edit');
            Route::post('/abouts/update','AboutController@update');

            Route::get('/terms/edit','TermController@edit');
            Route::post('/terms/update','TermController@update');
        });
    });

    Route::group(['prefix' => '/crone'],function()
    {
        Route::get('/users_packs','CroneController@users_packs');
        Route::get('/users_featured','CroneController@users_featured');
        Route::get('/slides','CroneController@slides');
        Route::get('/products_featured','CroneController@products_featured');
        Route::get('/products_discounts','CroneController@products_discounts');
        Route::get('/products_discounts_variations','CroneController@products_discounts_variations');
    });
});
