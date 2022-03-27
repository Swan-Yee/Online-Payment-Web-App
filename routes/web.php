<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// User Auth
Auth::routes(['reset' => false]);

// admin user auth
Route::prefix('admin')->namespace('Auth')->group(function(){
    Route::get('/login','AdminLoginController@showLoginForm')->name('admin');
    Route::post('/login','AdminLoginController@Login')->name('admin.login');
    Route::post('/logout','AdminLoginController@Logout')->name('admin.logout');
});

Route::middleware('auth')->namespace('Frontend')->group(function(){
    Route::get('/','PageController@home')->name('home');

    Route::get('/profile','PageController@profile')->name('profile');
    Route::get('/update-password','PageController@updatePassword')->name('update-password');
    Route::post('/update-password','PageController@updatePasswordStore')->name('update-password.store');

    Route::get('/wallet','PageController@wallet')->name('wallet');

    Route::get('/transfer','PageController@transfer')->name('transfer');
    Route::get('/transfer/confirm','PageController@transferConfirm')->name('transfer.confirm');
    Route::post('/transfer/complete','PageController@transferComplete')->name('transfer.complete');

    Route::get('/to-account-verify','PageController@toAccountVerify')->name('account.verify');

    Route::get('/password-check','PageController@passwordCheck')->name('account.password.check');

    Route::get('/transaction','PageController@transaction')->name('transaction');

    Route::get('/transaction/{id}','PageController@transactionDetail')->name('transaction.detail');

    Route::get('/transfer-hash','PageController@transferHash');

    Route::get('/qr-receive',"PageController@qrReceive")->name('qr-receive');

    Route::get('/qr-Scan',"PageController@qrScan")->name('qr-scan');

    Route::get('/scan-pay-form',"PageController@scanPayForm")->name('qr-scan-form');

    Route::get('/scan-pay/confirm',"PageController@scanPayConfirm")->name('qr-scan.confirm');

    Route::post('/scan-pay/complete',"PageController@scanPayComplete")->name('qr-scan.complete');

    Route::get('notification/','NotificationController@index')->name('noti');
    Route::get('notification/{id}','NotificationController@show')->name('noti.show');
    Route::get('/noti/complete','NotificationController@readAll');
});
