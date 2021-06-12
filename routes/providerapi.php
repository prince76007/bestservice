<?php

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

// Authentication
Route::post('/register' ,       'ProviderAuth\TokenController@register');
Route::post('/oauth/token' ,    'ProviderAuth\TokenController@authenticate');
Route::post('/otpverifysubmit' , 'ProviderAuth\TokenController@otp_verify_Submit');
Route::post('/resendotpverify' , 'ProviderAuth\TokenController@resend_otp_verify');

Route::post('/forgot/password',     'ProviderAuth\TokenController@forgot_password');
Route::post('/reset/password',      'ProviderAuth\TokenController@reset_password');

Route::group(['middleware' => ['provider.api']], function () {

    Route::group(['prefix' => 'profile'], function () {

        Route::get ('/' ,           'ProviderResources\ProfileController@index');
        Route::post('/' ,           'ProviderResources\ProfileController@update');
        Route::post('/password' ,   'ProviderResources\ProfileController@password');
        Route::post('/location' ,   'ProviderResources\ProfileController@location');
        Route::post('/available' ,  'ProviderResources\ProfileController@available');

    });

    Route::get('/target' , 'ProviderApiController@target');
    
    Route::get ('/services' ,    'ProviderApiController@services');
    Route::any ('/user' ,    'ProviderApiController@user');
    Route::any ('/provider_details' ,    'ProviderApiController@provider_details');
    Route::post ('/update/service' ,    'ProviderApiController@update_services');
    Route::post('/logout' , 'ProviderAuth\TokenController@logout');
    Route::resource('trip', 'ProviderResources\TripController');
    Route::post('cancel', 'ProviderResources\TripController@cancel');
    Route::get('summary', 'ProviderResources\TripController@summary');
    Route::get('help', 'ProviderResources\TripController@help_details');

    Route::group(['prefix' => 'trip'], function () {

        Route::post('{id}',             'ProviderResources\TripController@accept');
         Route::post('update/{id}',             'ProviderResources\TripController@update');
        Route::post('{id}/rate',        'ProviderResources\TripController@rate');
        Route::post('{id}/message' ,    'ProviderResources\TripController@message');

        Route::post('update_img/{id}' ,    'ProviderResources\TripController@update_image');
    });

    Route::group(['prefix' => 'requests'], function () 
	{
        Route::get('/upcoming' , 'ProviderApiController@upcoming_request');
        Route::get('/history',          'ProviderResources\TripController@history');
		
        Route::get('/history/details',  'ProviderResources\TripController@history_details');
        Route::get('/upcoming/details', 'ProviderResources\TripController@upcoming_details');

    });

});