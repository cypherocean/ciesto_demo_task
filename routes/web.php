<?php

use Illuminate\Support\Facades\Route;

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
Route::get('command:clear', function() {
    Artisan::call('cache:clear');
    Artisan::call('optimize:clear');
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    return "config, cache, and view cleared successfully";
});

Route::get('command:config', function() {
    Artisan::call('config:cache');
    return "config cache successfully";
});

Route::get('command:key', function() {
    Artisan::call('key:generate');
    return "Key generate successfully";
});

Route::get('command:migrate', function() {
    Artisan::call('migrate:refresh');
    return "Database migration generated";
});

Route::get('command:seed', function() {
    Artisan::call('db:seed');
    return "Database seeding generated";
});

Route::group(['middleware' => ['prevent-back-history']], function(){
    Route::group(['middleware' => ['guest']], function () {
        Route::get('/', 'AuthController@login')->name('login');
        Route::post('signin', 'AuthController@signin')->name('signin');

        Route::get('forgot-password', 'AuthController@forgot_password')->name('forgot.password');
        Route::post('password-forgot', 'AuthController@password_forgot')->name('password.forgot');
        Route::get('reset-password/{string}', 'AuthController@reset_password')->name('reset.password');
        Route::post('recover-password', 'AuthController@recover_password')->name('recover.password');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::get('logout', 'AuthController@logout')->name('logout');

        Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

        /** users */
            Route::any('user', 'UserController@index')->name('user');
            Route::get('user/create', 'UserController@create')->name('user.create');
            Route::post('user/insert', 'UserController@insert')->name('user.insert');
            Route::get('user/view/{id?}', 'UserController@view')->name('user.view');
            Route::get('user/edit/{id?}', 'UserController@edit')->name('user.edit');
            Route::patch('user/update', 'UserController@update')->name('user.update');
            Route::post('user/change-status', 'UserController@change_status')->name('user.change.status');
        /** users */
        
        /** Shop */
            Route::any('shop', 'ShopController@index')->name('shop');
            Route::get('shop/create', 'ShopController@create')->name('shop.create');
            Route::post('shop/insert', 'ShopController@insert')->name('shop.insert');
            Route::get('shop/view/{id?}', 'ShopController@view')->name('shop.view');
            Route::get('shop/edit/{id?}', 'ShopController@edit')->name('shop.edit');
            Route::patch('shop/update', 'ShopController@update')->name('shop.update');
            Route::post('shop/change-status', 'ShopController@change_status')->name('shop.change.status');
            Route::post('shop/remove-image', 'ShopController@remove_image')->name('shop.remove.image');
            Route::post('shop/import','ShopController@import')->name('shop.import');
            Route::get('shop/export','ShopController@export')->name('shop.export');
        /** Shop */
    
        /** Products */
            Route::any('products', 'ProductController@index')->name('products');
            Route::get('products/create', 'ProductController@create')->name('products.create');
            Route::post('products/insert', 'ProductController@insert')->name('products.insert');
            Route::get('products/view/{id?}', 'ProductController@view')->name('products.view');
            Route::get('products/edit/{id?}', 'ProductController@edit')->name('products.edit');
            Route::patch('products/update', 'ProductController@update')->name('products.update');
            Route::post('products/change-status', 'ProductController@change_status')->name('products.change.status');
            Route::post('products/remove-image', 'ProductController@remove_image')->name('products.remove.image');
            Route::post('products/import','ProductController@import')->name('products.import');
            Route::get('products/export','ProductController@export')->name('products.export');
        /** Products */
    });
});
