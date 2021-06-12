<?php

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    echo "Cache is cleared";
});

Auth::routes();

Route::group(['prefix' => 'provider'], function () {
    Route::get('/login', 'ProviderAuth\LoginController@showLoginForm');
    Route::post('/login', 'ProviderAuth\LoginController@login');
    Route::post('/logout', 'ProviderAuth\LoginController@logout');

    Route::get('/register', 'ProviderAuth\RegisterController@showRegistrationForm');
	Route::post('/register2', 'ProviderAuth\RegisterController@showRegistrationForm2');
    Route::post('/register', 'ProviderAuth\RegisterController@register');

    Route::post('/password/email', 'ProviderAuth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/password/reset', 'ProviderAuth\ResetPasswordController@reset');
    Route::get('/password/reset', 'ProviderAuth\ForgotPasswordController@showLinkRequestForm');
    Route::get('/password/reset/{token}', 'ProviderAuth\ResetPasswordController@showResetForm');
});

Route::group(['prefix' => 'admin'], function () {
    Route::get('/login', 'AdminAuth\LoginController@showLoginForm');
    Route::post('/login', 'AdminAuth\LoginController@login');
    Route::post('/logout', 'AdminAuth\LoginController@logout');

    Route::post('/password/email', 'AdminAuth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/password/reset', 'AdminAuth\ResetPasswordController@reset');
    Route::get('/password/reset', 'AdminAuth\ForgotPasswordController@showLinkRequestForm');
    Route::get('/password/reset/{token}', 'AdminAuth\ResetPasswordController@showResetForm');
});

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

Route::get('/', function () {
    return view('index');
});

Route::get('/ride', function () {
    return view('ride');
});

Route::get('/drive', function () {
    return view('drive');
});

Route::get('view_pages/{id}','PagesController@view_pages');


Route::get('privacy', function () {
    $page = 'page_privacy';
    $title = 'Privacy Policy';
    return view('static', compact('page', 'title'));
});

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/otp',  'HomeController@otp');

Route::get('/mobilenumbersubmit', 'HomeController@mobilenumbersubmit');
Route::post('/mobilenumbersubmited', 'HomeController@mobilenumbersubmited');

Route::post('/otpsubmit', 'HomeController@otpSubmit');
Route::get('/resendotp', 'HomeController@resendotp');


Route::get('/dashboard', 'HomeController@index');
Route::post('/create/ride2', 'HomeController@index2');
// user profiles
Route::get('/profile',      'HomeController@profile');
Route::get('/edit/profile', 'HomeController@edit_profile');
Route::post('/profile',     'HomeController@update_profile');



// update password
Route::get('/change/password',  'HomeController@change_password');
Route::post('/change/password', 'HomeController@update_password');

// ride
Route::get('/confirm/ride', 'RideController@confirm_ride');
Route::post('/create/ride', 'RideController@create_ride');

Route::post('/cancel/ride', 'RideController@cancel_ride');
Route::get('/onride',       'RideController@onride');
Route::post('/payment',     'PaymentController@payment');
Route::post('/rate',        'RideController@rate');

//payumaoney

Route::get('payment-process', [
    'as' => 'payment-process',
    'uses' => 'PaymentController@paymentProcess'
]);


Route::any('payment-cancel1', [
    'as' => 'payment-cancel1',
    'uses' => 'PaymentController@paymentCancel1'
]);

Route::any('payment-response1', [
    'as' => 'payment-response1',
    'uses' => 'PaymentController@paymentResponse1'
]);




Route::any('payment-cancel', [
    'as' => 'payment-cancel',
    'uses' => 'PaymentController@paymentCancel'
]);

Route::any('payment-response', [
    'as' => 'payment-response',
    'uses' => 'PaymentController@paymentResponse'
]);


Route::post('load-wallet-payment-cancel', [
    'as' => 'load-wallet-payment-cancel',
    'uses' => 'PaymentController@LoadwalletpaymentCancel'
]);

Route::post('load-wallet-payment-response', [
    'as' => 'load-wallet-payment-response',
    'uses' => 'PaymentController@LoadwalletpaymentResponse'
]);

// status check
Route::any('/status', 'RideController@status');

// trips
Route::get('/trips', 'HomeController@trips');
Route::get('/my_request', 'HomeController@my_request');
Route::get('/help', 'HomeController@help');

// wallet
Route::get('/wallet',       'HomeController@wallet');
Route::post('/loadwallet',  'HomeController@loadwallet');

Route::post('/add/money',   'PaymentController@add_money');

Route::post('/add/savemoney',   'PaymentController@saveMoney');

// payment
Route::get('/payment', 'HomeController@payment');

// card
Route::resource('card', 'Resource\CardResource');

// promotions
Route::get('/promotion',        'HomeController@promotion');
Route::post('/add/promocode',   'HomeController@add_promocode');

// upcoming
Route::get('/upcoming/trips', 'HomeController@upcoming_trips');

// send push notification
Route::get('/send/push',
    function(){
        $data = PushNotification::app('IOSUser')
        ->to('163e4c0ca9fe084aabeb89372cf3f664790ffc660c8b97260004478aec61212c')
        ->send('Hello World, i`m a push message');
            dd($data);
    });

Route::get('sig','api_b2b@sig');
Route::get('sig2','api_b2b@sig2');
Route::get('check_provider_email','api_b2b@check_provider_email');
Route::get('check_user_email','api_b2b@check_user_email');

Route::get('forget_p','api_b2b@forget_p');
Route::get('check_user_otp','api_b2b@check_user_otp');
Route::get('add_money_to_my_account','api_b2b@add_money_to_my_account');


Route::get('redirect2',function (){echo "okkk";});

Route::get('redirect3', 'SocialAuthFacebookController@redirect');
Route::get('callback3', 'SocialAuthFacebookController@callback');
Route::get('/redirect', 'SocialAuthGoogleController@redirect');
Route::get('/callback', 'SocialAuthGoogleController@callback');





Route::get('subscribe-process', ['as' => 'subscribe-process','uses' => 'SigninController@SubscribProcess']);
Route::get('subscribe-cancel', ['as' => 'subscribe-cancel','uses' => 'SigninController@SubscribeCancel']);
Route::get('subscribe-response', ['as' => 'subscribe-response','uses' => 'SigninController@SubscribeResponse']);


Route::any('get_nearby_provider','api_b2b@get_nearby_provider');
Route::get('/add_slider', function () { return view('admin/users/add_slider'); });
Route::get('/all_slider', function () { return view('admin/users/all_slider'); });
Route::get('/all_slider_as_provider', function () { return view('admin/users/all_slider_as_provider'); });

Route::get('/add_slider1', function () { echo "Ok"; });

Route::post('insert_slider', 'api_b2b@insert_slider');
Route::get('/delete_slider/{id}', 'api_b2b@delete_slider');
Route::get('/edit_slider/{id}', 'api_b2b@edit_slider');
Route::post('update_slider', 'api_b2b@update_slider');

Route::get('price_of_provider','api_b2b@price_of_provider');
Route::get('get_ser_status','api_b2b@get_ser_status');


 Route::get('get_slider_for_user', 'api_b2b@get_slider_for_user');
 Route::get('get_slider_for_provider', 'api_b2b@get_slider_for_provider');

Route::get('check_out', 'api_b2b@check_out');
Route::get('check_out/{id}/{id2}', 'api_b2b@check_outs');
Route::post('ser_bef_image', 'api_b2b@ser_bef_image');
//Route::get('check', 'api_b2b@check');
Route::post('/add_slider99', function () { return view('admin/users/add_slider99'); });

Route::post('send_notifi', 'api_b2b@send_notifi');
Route::post('update_fcm_token', 'api_b2b@update_fcm_token');
Route::post('update_provider', 'api_b2b@update_provider');
Route::post('update_service_status', 'api_b2b@update_service_status');
Route::post('update_service_statusBYuser', 'api_b2b@update_service_statusBYuser');


Route::get('all_request', 'api_b2b@all_request');

//create Page
Route::get('create_page','CreatepageController@createpage');
Route::post('storepage','CreatepageController@storepage')->name('storepage');
Route::get('pages/{id}','PagesController@index');
Route::any('admin/update_page','PagesController@update_page');