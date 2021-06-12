<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Provider;
use App\ProviderDevice;
use App\Http\Controllers\ProviderResources\TripController;
use Exception;
use Log;
use Setting;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class SendPushNotification extends Controller
{
    /**
     * New Ride Accepted by a Driver.
     *
     * @return void
     */
    public function RideAccepted($request)
    {
        //sms($request->user_id, trans('api.push.request_accepted'));
        return $this->sendPushToUser($request->user_id, trans('api.push.request_accepted'));
    }

    /**
     * Driver Arrived at your location.
     *
     * @return void
     */
    public function user_schedule($user)
    {
        //sms($user, trans('api.push.schedule_start'));
        return $this->sendPushToUser($user->user_id, trans('api.push.schedule_start'));
    }
        public function user_dropped($user)
    {
        //sms($user, trans('api.push.schedule_start'));
        return $this->sendPushToUser($user->user_id, trans('api.push.dropped'));
    }
    
    

    /**
     * New Incoming request
     *
     * @return void
     */
    public function provider_schedule($provider)
    {
        //smsPro($provider, trans('api.push.schedule_start'));
       // dd( $this->sendPushToProvider($provider, trans('api.push.schedule_start')));
        return $this->sendPushToProvider($provider, trans('api.push.schedule_start'));
    }

    /**
     * New Ride Accepted by a Driver.
     *
     * @return void
     */
    public function UserCancellRide($request)
    {
        //smsPro($request->provider_id, trans('api.push.user_cancelled'));
        return $this->sendPushToProvider($request->provider_id, trans('api.push.user_cancelled'));
    }


    /**
     * New Ride Accepted by a Driver.
     *
     * @return void
     */
    public function ProviderCancellRide($request)
    {
        //sms($request->user_id, trans('api.push.provider_cancelled'));
        return $this->sendPushToUser($request->user_id, trans('api.push.provider_cancelled'));
    }

    /**
     * Driver Arrived at your location.
     *
     * @return void
     */
    public function Arrived($request)
    {
        //sms($request->user_id, trans('api.push.arrived'));
        return $this->sendPushToUser($request->user_id, trans('api.push.arrived'));
    }
    
    

    /**
     * Driver Arrived at your location.
     *
     * @return void
     */
    public function Dropped($request)
    {

        Log::info(trans('api.push.dropped') . Setting::get('currency') . $request->payment->total . ' by ' . $request->payment_mode);
        //sms($request->user_id, trans('api.push.dropped') . Setting::get('currency') . $request->payment->total . ' by ' . $request->payment_mode);
        return $this->sendPushToUser($request->user_id, trans('api.push.dropped') . Setting::get('currency') . $request->payment->total . ' by ' . $request->payment_mode);
    }

    /**
     * Money added to user wallet.
     *
     * @return void
     */
    public function ProviderNotAvailable($user_id)
    {
        //sms($user_id, trans('api.push.provider_not_available'));
        return $this->sendPushToUser($user_id, trans('api.push.provider_not_available'));
    }

    /**
     * New Incoming request
     *
     * @return void
     */
    public function IncomingRequest($provider)
    {
        //smsPro($provider, trans('api.push.incoming_request'));
        return $this->sendPushToProvider($provider, trans('api.push.incoming_request'));
    }

    /**
     * New Change request
     *
     * @return void
     */
    public function ChangeRequest($provider)
    {
        //smsPro($provider, trans('api.ride.request_modify_location'));
        return $this->sendPushToProvider($provider, trans('api.ride.request_modify_location'));
    }


    /**
     * Driver Documents verfied.
     *
     * @return void
     */
    public function DocumentsVerfied($provider_id)
    {
        //smsPro($provider_id, trans('api.push.document_verfied'));
        return $this->sendPushToProvider($provider_id, trans('api.push.document_verfied'));
    }


    /**
     * Money added to user wallet.
     *
     * @return void
     */
    public function WalletMoney($user_id, $money)
    {

        return $this->sendPushToUser($user_id, $money . ' ' . trans('api.push.added_money_to_wallet'));
    }

    /**
     * Money charged from user wallet.
     *
     * @return void
     */
    public function ChargedWalletMoney($user_id, $money)
    {

        return $this->sendPushToUser($user_id, $money . ' ' . trans('api.push.charged_from_wallet'));
    }

    /**
     * Sending Push to a user Device.
     *
     * @return void
     */
    public function sendPushToUser($user_id, $push_message)
    {

        try {

            $user = User::findOrFail($user_id);

            if ($user->device_token != "") {

                Log::info('sending push for user : ' . $user->first_name);
                //SendPushNotification::sms($user_id, $push_message);
                if ($user->device_type == 'ios') {

                    return \PushNotification::app('IOSUser')
                        ->to($user->device_token)
                        ->send($push_message);
                } elseif ($user->device_type == 'android') {

                    $optionBuilder = new OptionsBuilder();
                    $optionBuilder->setTimeToLive(60 * 20);

                    $notificationBuilder = new PayloadNotificationBuilder((env("APP_NAME", "dragon")));
                    $notificationBuilder->setBody($push_message)
                        ->setSound('default');

                    $dataBuilder = new PayloadDataBuilder();
                    $dataBuilder->addData(['a_data' => 'my_data']);

                    $option = $optionBuilder->build();
                    $notification = $notificationBuilder->build();
                    $data = $dataBuilder->build();

                    $token = $user->device_token;

                    $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
                    Log::info("fcm run");
                    $downstreamResponse->numberSuccess();
                    $downstreamResponse->numberFailure();


                    return \PushNotification::app('AndroidUser')
                        ->to($user->device_token)
                        ->send($push_message);
                }
            }
        } catch (Exception $e) {
            return $e;
        }
    }

    /**
     * Sending Push to a user Device.
     *
     * @return void
     */
    public function sendPushToProvider($provider_id, $push_message)
    {

        try {

            $provider = ProviderDevice::where('provider_id', $provider_id)->with('provider')->first();

            if ($provider->token != "") {

                Log::info('sending push for provider : ' . $provider->provider->first_name);
                //SendPushNotification::smsPro($provider_id, $push_message);
                if ($provider->type == 'ios') {

                    return \PushNotification::app('IOSProvider')
                        ->to($provider->token)
                        ->send($push_message);
                } elseif ($provider->type == 'android') {

                    $optionBuilder = new OptionsBuilder();
                    $optionBuilder->setTimeToLive(60 * 20);

                    $notificationBuilder = new PayloadNotificationBuilder(env("APP_NAME", "dragon"));
                    $notificationBuilder->setBody($push_message)
                        ->setSound('default');

                    $dataBuilder = new PayloadDataBuilder();
                    $dataBuilder->addData(['a_data' => 'my_data']);

                    $option = $optionBuilder->build();
                    $notification = $notificationBuilder->build();
                    $data = $dataBuilder->build();

                    $token = $provider->token;

                    $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
                    Log::info("fcm run");
                    Log::info($downstreamResponse);
                    $downstreamResponse->numberSuccess();
                    $downstreamResponse->numberFailure();

                    return \PushNotification::app('AndroidProvider')
                        ->to($provider->token)
                        ->send($push_message);
                }
            }
        } catch (Exception $e) {
            return $e;
        }
    }
    /**
     * Sending Push to a user Device.
     *
     * @return void
     */
    public function sms($user_id, $push_message)
    {
        try {
            $username = "Deocabs";
            $password = "Deocabs#123";
            $sender = "DEOCAB";
            //$user = User::findOrFail($user_id);
            $mobile = User::where('id', $user_id)->first()->mobile;
            log::alert($mobile);
            log::alert($push_message);
            $final = urlencode($push_message);
            $url = "login.bulksmsgateway.in/sendmessage.php?user=" . urlencode($username) . "&password=" . urlencode($password) . "&mobile=" . urlencode($mobile) . "&sender=" . urlencode($sender) . "&message=" . urlencode($push_message) . "&type=" . urlencode('3');
            log::alert($url);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "",
                CURLOPT_HTTPHEADER => array(
                    "Postman-Token: c9c6630f-ad5a-40c4-b54c-7f6ec65d3981",
                    "cache-control: no-cache"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
        } catch (Exception $e) {
            return $e;
        }
    }

    /**
     * Sending Push to a user Device.
     *
     * @return void
     */
    public function smsPro($provider_id, $push_message)
    {
        try {
            $username = "Deocabs";
            $password = "Deocabs#123";
            $sender = "DEOCAB";
            $provider = ProviderDevice::where('provider_id', $provider_id)->with('provider')->first();
            //$mobile = User::whereuser_id($request->user_id)->get('mobile');
            $mobile = Provider::where('provider_id', $provider_id)->first()->mobile;
            log::alert($mobile);
            $final = urlencode($push_message);
            $url = "login.bulksmsgateway.in/sendmessage.php?user=" . urlencode($username) . "&password=" . urlencode($password) . "&mobile=" . urlencode($mobile) . "&sender=" . urlencode($sender) . "&message=" . urlencode($push_message) . "&type=" . urlencode('3');
            log::alert($url);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "",
                CURLOPT_HTTPHEADER => array(
                    "Postman-Token: c9c6630f-ad5a-40c4-b54c-7f6ec65d3981",
                    "cache-control: no-cache"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
        } catch (Exception $e) {
            return $e;
        }
    }
}

