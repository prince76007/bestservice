<?php

/*
|-----------https://www.getpostman.com/collections/fe842a85958f3ed11442---------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/




Route::post('/signup' , 'UserApiController@signup');
Route::post('/otpverifysubmit' , 'UserApiController@otp_verify_Submit');
Route::post('/resendotpverify' , 'UserApiController@resend_otp_verify');
Route::post('/logout' , 'UserApiController@logout');
Route::post('/forgot/password',     'UserApiController@forgot_password');
Route::post('/reset/password',      'UserApiController@reset_password');
	//referral code
Route::post('/getrefcode','UserApiController@get_user_referralcode');
Route::post('/getrefdata','UserApiController@get_referral_data');
Route::post('/isrefcodesubmited','UserApiController@isrefcodesubmited');
Route::get('/services' , 'UserApiController@services');
Route::group(['middleware' => ['auth:api']], function () {

	// user profile

	Route::post('/change/password' , 	'UserApiController@change_password');
	Route::post('/update/location' , 	'UserApiController@update_location');
	Route::get('/details' , 			'UserApiController@details');
	Route::get('/provider' , 			'UserApiController@provider');
	Route::post('/update/profile' , 	'UserApiController@update_profile');

	// services

	// Route::get('/services' , 'UserApiController@services');

	// provider

	Route::post('/rate/provider' , 'UserApiController@rate_provider');

	// request

	Route::post('/send/request' , 	'UserApiController@send_request');
	Route::post('/cancel/request' , 'UserApiController@cancel_request');
	Route::any('/request/check' , 	'UserApiController@request_status_check');

	// history

	Route::get('/trips' , 			'UserApiController@trips');
	Route::get('/trip/details' , 	'UserApiController@trip_details');

	// payment

	Route::post('/payment' , 	'PaymentController@payment');
	Route::post('/add/money' , 	'PaymentController@add_money');

	// estimated

	Route::get('/estimated/fare' , 'UserApiController@estimated_fare');

	// promocode

	Route::get('/promocodes' , 		'UserApiController@promocodes');
	Route::post('/promocode/add' , 	'UserApiController@add_promocode');

	// card payment

    Route::resource('card', 'Resource\CardResource');

    Route::get('/show/providers' , 'UserApiController@show_providers');
    
    Route::get('upcoming/trips' , 'UserApiController@upcoming_trips');
    Route::get('upcoming/trip/details' , 'UserApiController@upcoming_trip_details');

	Route::get('/help' , 'UserApiController@help_details');

});
