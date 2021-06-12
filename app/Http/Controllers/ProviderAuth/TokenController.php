<?php
namespace App\Http\Controllers\ProviderAuth;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Notifications\ResetPasswordOTP;
use Auth;
use Config;
use Setting;
use JWTAuth;
use Exception;
use Notification;
use App\Provider;
use App\ProviderDevice;
use Mail;
use DB;

class TokenController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


     public function resend_otp_verify(Request $request){


        $this->validate($request, [
                'id' => 'required',
                ]); 

        try{
           $User_id = $request->id;
           $user = DB::table('providers')->where('id' , $User_id)->first();
           if(isset($user->id)){ 
              $mobile =$user->mobile;
              $otp =rand(0000,9999);
              $otpsms=" Your Otp is: ".$otp;
             $User['sms']=send_sms($mobile,$otpsms);
                 DB::table('providers')
                ->where('id', $user->id)
                ->update(['otp' => $otp , 'provider_verify_flag'=> 0 ]);           
            
             $User = DB::table('providers')->where('id' , $User_id)->first();
              return response()->json(['success' => trans('Otp Resend Successfully') , 'data' => $User] , 200);
             //return $User;
            }else{
             return response()->json(['error' => trans('Provider User Not Found')], 200);
            } 
           } catch (Exception $e) {
             return response()->json(['error' => trans('api.something_went_wrongS')], 500);
        }   
    }

    public function otp_verify_Submit(Request $request)
    {

        $this->validate($request, [
                'id' => 'required',
                'otp' => 'required',
                'device_token' => 'required',
                'device_id' => 'required',
                'device_type' => 'in:android,ios',
                ]); 

        try{
            
            $user_id = $request->id;
            $otp = $request->otp;
        
            $User =  DB::table('providers')->where('id' , $user_id)->first();
                   
            if(isset($User->id)){
            
               $User=DB::table('providers')->where('id' , $user_id)->where( 'otp' , $otp)->first();
               if($User){ 
                 DB::table('providers')
                ->where('id', $User->id)
                ->update(['otp' => 0 , 'provider_verify_flag'=> 1 ]);           
            
                 $User = DB::table('providers')->where('id' , $User->id)->first();
                  //return $User;
                 return response()->json(['success' => trans('Otp Verify Successfully') , 'data' => $User] , 200);
             
                }else{
              return response()->json(['error' => trans('Invalid Otp ')], 200);
                }
            }
            else{
             return response()->json(['error' => trans('Provider User Not Found')], 200);
            } 
        
        
        } catch (Exception $e) {
             return response()->json(['error' => trans('something_went_wrongS')], 500);
        }
    }



    public function register(Request $request)
    {   
        
        $this->validate($request, [
                'device_id' => 'required',
                'device_type' => 'required|in:android,ios',
                'device_token' => 'required',
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:providers',
                'mobile' => 'required|unique:providers',
                'password' => 'required|min:6',
                'latitude' => 'required',
                'longitude' => 'required',
            //    'call'=> 'required'
            ]);

        
     
        try{

            
              $otp =rand(1000,9999);
              $otpsms=" Your Otp is: ".$otp;
              
            $Provider = $request->all();
            $Provider['password'] = bcrypt($request->password);
            $Provider['status'] = 'approved';
            $Provider['otp'] = $otp;
            $Provider['provider_verify_flag'] = 0;
            $Provider['uniq_no']=getProviderId();
            
            $Provider = Provider::create($Provider);

            ProviderDevice::create([
                    'provider_id' => $Provider->id,
                    'udid' => $request->device_id,
                    'token' => $request->device_token, 
                    'type' => $request->device_type
                    
                ]);
            $Provider['sms'] ="not fire";
            if(isset($Provider->id)){

                      $nProvider = Provider::findOrFail($Provider->id);
                      $nProvider->otp = $otp;
                      $nProvider->provider_verify_flag=0;
                      $nProvider->save();

            $mobile=$Provider->mobile;     
            $Provider['sms'] =send_sms($mobile,$otpsms);    
            $Provider['otp'] = $otp;
            $Provider['provider_verify_flag'] = 0;
            $Provider['uniq_no'] = $nProvider->uniq_no;
            }

            return $Provider;


        } catch (QueryException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Something went wrong, Please try again later!'], 500);
            }
            return abort(500);
        }
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function authenticate(Request $request)
    {
        $this->validate($request, [
                'device_id' => 'required',
                'device_type' => 'required|in:android,ios',
                'device_token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

        Config::set('auth.providers.users.model', 'App\Provider');

        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'The email address or password you entered is incorrect.'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Something went wrong, Please try again later!'], 500);
        }

        $User = Provider::with('service', 'device')->find(Auth::user()->id);
        
        // if($User->provider_verify_flag==0){
        //     return response()->json(['error' => 'Please Pass OTP Verification'], 501);
        // }
        $User->access_token = $token;
        $User->currency = Setting::get('currency', '$');

        if($User->device) {
            if($User->device->token != $request->token) {
                $User->device->update([
                        'udid' => $request->device_id,
                        'token' => $request->device_token,
                        'type' => $request->device_type,
                    ]);
            }
        } else {
            ProviderDevice::create([
                    'provider_id' => $User->id,
                    'udid' => $request->device_id,
                    'token' => $request->device_token,
                    'type' => $request->device_type,
                ]);
        }

        return response()->json($User);
    }



 /**
     * Forgot Password.
     *
     * @return \Illuminate\Http\Response
     */


    public function forgot_password(Request $request){

        $this->validate($request, [
                'email' => 'required|email|exists:providers,email',
            ]);

        try{  
            
            $provider = Provider::where('email' , $request->email)->first();

            $otp = mt_rand(100000, 999999);

            $provider->otp = $otp;
            $provider->save();
			
            $to_name = 'ro';
			$to_email = $request->email;
			$last_id_booking = 0;
			$data  = 'OK';
			$data = array('name'=>"Ogbonna Vitalis(sender_name)","id"=>$otp, "body" => "",'response'=>$data);
			Mail::send('email', ['data'=>$data], function($message) use ($to_name, $to_email) {
			$message->to($to_email, $to_name)
			->subject("Best Service Forget Password");
			$message->from('mohdsalman626@gmail.com'," Best Service");
			});
            //Notification::send($provider, new ResetPasswordOTP($otp));

            return response()->json([
                'message' => 'OTP sent to your email!',
                'provider' => $provider
            ]);

        }catch(Exception $e){
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }


    /**
     * Reset Password.
     *
     * @return \Illuminate\Http\Response
     */

    public function reset_password(Request $request){

        $this->validate($request, [
                'password' => 'required|confirmed|min:6',
                'id' => 'required|numeric|exists:providers,id'
            ]);

        try{

            $Provider = Provider::findOrFail($request->id);
            $Provider->password = bcrypt($request->password);
            $Provider->save();

            if($request->ajax()) {
                return response()->json(['message' => 'Password Updated']);
            }

        }catch (Exception $e) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')]);
            }
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function logout(Request $request)
    {
        try {
            ProviderDevice::where('provider_id', $request->id)->update(['udid'=> '', 'token' => '']);
            return response()->json(['message' => trans('api.logout_success')]);
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }
    
}
