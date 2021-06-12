<?php

/*
|--------------------------------------------------------------------------
| Provider Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 		'ProviderController@index')->name('index');
Route::get('/trips', 	'ProviderResources\TripController@history')->name('trips');

Route::get('/incoming', 			'ProviderController@incoming')->name('incoming');
Route::post('/request/{id}', 		'ProviderController@accept')->name('accept');
Route::patch('/request/{id}', 		'ProviderController@update')->name('update');
Route::post('/request/{id}/rate', 	'ProviderController@rating')->name('rating');
Route::delete('/request/{id}', 		'ProviderController@reject')->name('reject');

Route::get('/earnings', 'ProviderController@earnings')->name('earnings');

Route::resource('documents', 'ProviderResources\DocumentController');

Route::get('/profile', 	'ProviderResources\ProfileController@show')->name('profile.index');
Route::get('/allservices', 	'ProviderResources\ProfileController@allservices')->name('profile.allservices');
Route::post('/profile', 'ProviderResources\ProfileController@store')->name('profile.update');

Route::get('/location', 	'ProviderController@location_edit')->name('location.index');
Route::post('/location', 	'ProviderController@location_update')->name('location.update');

Route::post('/profile/available', 	'ProviderController@available')->name('available');
Route::get('/profile/password', 'ProviderController@change_password')->name('change.password');
Route::post('/change/password', 'ProviderController@update_password')->name('password.update');

Route::get('/upcoming', 'ProviderController@upcoming_trips')->name('upcoming');
Route::get('/upcoming222', 'ProviderController@upcoming_trips222')->name('upcoming222');


Route::post('/cancel', 'ProviderController@cancel')->name('cancel');