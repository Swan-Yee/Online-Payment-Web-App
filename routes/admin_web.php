<?php

use App\Http\Controllers\Backend\PageController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->namespace('Backend')->middleware('auth:admin_user')->group(function(){
    Route::get('/','PageController@home')->name('home');
    Route::resource('admin-user','AdminUserController');
    Route::get('admin-user/datatable/ssd','AdminUserController@ssd');

    Route::resource('user','UserController');
    Route::get('user/datatable/ssd','UserController@ssd');

    Route::get('/wallet','WalletController@index')->name('wallet.index');
    Route::get('wallet/datatable/ssd','WalletController@ssd');

    Route::get('/wallet/add/amount','WalletController@addAmount');
    Route::post('/wallet/add/amount','WalletController@storeAddAmount');
    Route::get('/wallet/reduce/amount','WalletController@reduceAmount');
    Route::post('/wallet/reduce/amount','WalletController@storeReduceAmount');
});
