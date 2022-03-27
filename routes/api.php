<?php

use App\User;
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

Route::namespace('Api')->group(function(){
    Route::post('/register','AuthController@register');
    Route::post('/login','AuthController@login');

    Route::get('test',function(){
        return User::all();
    });

    Route::middleware('auth:api')->group(function(){
        Route::get('/profile','PageController@profile');
        Route::get('/logout','AuthController@logout');
        Route::get('/transaction','PageController@transaction');
        Route::get('/transaction/{id}','PageController@transactionDetail');
        Route::get('/notifaction','PageController@noti');
        Route::get('/notifaction/{id}','PageController@notiDetail');
        Route::get('/to-account-verify','PageController@accountVerify');
        Route::get('/transfer/confirm','PageController@transferConfirm');
        Route::post('/transfer/complete','PageController@transferComplete');
        Route::get('/qr-form','PageController@scanPayForm');
        Route::get('/qr-form/confirm','PageController@scanPayConfirm');
        Route::get('/qr-form/complete','PageController@scanPayComplete');
    });
});
