<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Log;
use Auth;
use Hash;
use Setting;
use Exception;
use Notification;
use Storage;
use Carbon\Carbon;
use App\Http\Controllers\SendPushNotification;
use App\Notifications\ResetPasswordOTP;
use App\Http\Controllers\ProviderResources\TripController;
use App\User;
use App\ProviderService;
use App\UserRequests;
use App\Promocode;
use App\RequestFilter;
use App\ServiceType;
use App\ServiceSubType;
use App\Provider;
use App\Settings;
use App\UserRequestRating;
use App\Card;
use App\PromocodeUsage;
use App\Helpers\Helper;
use Mail;
use DB;
class UserApiController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


/*
    public function resend_otp_verify(Request $request){


        $this->validate($request, [
                'mobile' => 'required|unique:users',
                ]); 

        try{
           $mobile = $request->mobile;
        
          
            
              $otp =rand(0000,9999);
              $otpsms=" Your Otp is: ".$otp;
            send_sms($mobile,$otpsms);
                         
            $User['otp']= $otp ;
            
              return response()->json(['success' => trans('otp Resend Successfully') , 'data' => $User] , 200);
       
           } catch (Exception $e) {
             return response()->json(['error' => trans('api.something_went_wrongS')], 500);
        }   
    }
*/
    public function resend_otp_verify(Request $request){


        $this->validate($request, [
                'id' => 'required',
                ]); 

        try{
           $User_id = $request->id;
           $user = DB::table('users')->where('id' , $User_id)->first();
           if(isset($user->id)){ 
              $mobile =$user->mobile;
              $otp=rand(1000,9999);
              $otpsms="Dear,  Your Otp is: ".$otp;
             $User['sms']=send_sms($mobile,$otpsms);
                 DB::table('users')
                ->where('id', $user->id)
                ->update(['otp' => $otp , 'verify_flag'=> 0 ]);           
            
             $User = DB::table('users')->where('id' , $User_id)->first();
              return response()->json(['success' => trans('otp Resend Successfully') , 'data' => $User] , 200);
             //return $User;
            }else{
             return response()->json(['error' => trans('User Not Found')], 200);
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
        
            $User =  DB::table('users')->where('id' , $user_id)->first();
                   
            if(isset($User->id)){
            
               $User=DB::table('users')->where('id' , $user_id)->where( 'otp' , $otp)->first();
               if($User){ 
                 DB::table('users')
                ->where('id', $User->id)
                ->update(['otp' => 0 , 'verify_flag'=> 1 ]);           
            
                 $User = DB::table('users')->where('id' , $User->id)->first();
                  //return $User;
                 return response()->json(['success' => trans('otp_verify Successfully') , 'data' => $User] , 200);
             
                }else{
              return response()->json(['error' => trans('Invalid Otp')], 200);
                }
            }
            else{
             return response()->json(['error' => trans('User Not Found')], 200);
            } 
        
        
        } catch (Exception $e) {
             return response()->json(['error' => trans('something_went_wrongS')], 500);
        }
    }


    public function get_user_referralcode(Request $request){
        try{
            $User = Auth::user();
            if($User==null){
                    $User=User::findOrFail($request->id);
                }
                return response()->json(['code' => trans($User['referral_id'])], 200);
        }catch(Exception $e){
            return response()->json(['error' => trans('api.something_went_wrongS')], 500);
        }
    }


    public function isrefcodesubmited(Request $request){
        try{
            $User = Auth::user();
            if($User==null || $User==''){
                    $User=User::findOrFail($request->id);
                }
                    $data=DB::table('referral_data')->where('user_id',$User['id'])->first();
                    if($data!=null &&$data!=''){
                    if(($data->referral_by==null || $data->referral_by=='') && $data->status=="active"){
                        if($request->referral_code!=null && $request->referral_code!=''){
                           // DB::table('referral_data')->insert($refcodeData);
                           DB::table('referral_data')->where('user_id',$User['id'])
                            ->update(['referral_by' => $request->referral_code]);
                            return response()->json(['status' =>trans("Referral Code added Succesfully")], 200);
                        }
                        return response()->json(['status' =>trans("0")], 200);
                    }else{
                        return response()->json(['status' =>trans("1")], 200);
                    }
                }else{
                    //$newstring = substr($User['mobile'], -4);

                   // $User['referral_id']=$User['first_name'].'_'.$newstring;
                    
                    $data=array("user_id"=>$User['id']);
                  DB::table('referral_data')->insert($data);
                    return response()->json(['status' =>trans("0")], 200);
                }
        }catch(Exception $e){
            return response()->json(['error' => trans($e->getMessage())], 500);
        }
    }

    public function get_referral_data(Request $request){
        try{
            $User = Auth::user();
            if($User==null || $User==''){
                    $User=User::findOrFail($request->id);
                }
                if(DB::table('referral_data')->where('referral_by',$User['referral_id'])->exists()){
               $data=DB::table('referral_data')->where('referral_by',$User['referral_id'])->get();
               $users=array();
               foreach($data as $subData){
                   $subData=(array)$subData;
                $usedBy=User::where('id' ,$subData['user_id'])->first();//all data from table
                    if($usedBy!=null && $usedBy!=''){
                        $usedByRefined['id']=$usedBy['id'];
                        $usedByRefined['picture']=$usedBy['picture'];
                        $usedByRefined['first_name']=$usedBy['first_name'];
                        $usedByRefined['last_name']=$usedBy['last_name'];
                        if(strcasecmp($subData['status'],"active")==0){
                            $usedByRefined['status']="Pending";
                        }elseif(strcasecmp($subData['status'],"deactive")==0){
                            $usedByRefined['status']="Success";
                        }
                        array_push($users,$usedByRefined);
                    }
               }
                    return response()->json(['refdata' =>$users], 200);
                }else{
                    return response()->json(['refdata' => trans('Data Not Found')], 200);
                }
        }catch(Exception $e){
            return response()->json(['error' => trans($e->getMessage())], 500);
        }
    }

    public function signup(Request $request)
    {
    
            $this->validate($request, [
                'social_unique_id' => ['required_if:login_by,facebook,google','unique:users'],
                'device_type' => 'required|in:android,ios',
                'device_token' => 'required',
                'device_id' => 'required',
                'login_by' => 'required|in:manual,facebook,google',
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users',
                'mobile' => 'required|unique:users',
                'password' => 'required|min:6',
            ]); 
          
        try{

            $User = $request->all();
            
            $otp=rand(1000,9999);
              
              $otpsms="Dear,  Your Otp is: ".$otp;
            $User['otp'] = $otp;
            $User['verify_flag'] = '0';
          


            $User['payment_mode'] = 'CASH';
            $User['password'] = bcrypt($request->password);
            $exist=User::where('email',$request->email)->orwhere('mobile',$request->mobile)->where('verify_flag',0)->first();
      
            if(!empty($exist)){
              
                    $User= User::findOrFail($exist->id);
                    $User->device_id=$request->device_id;
                    $User->device_type=$request->device_type;
                    $User->device_token=$request->device_token;
                    $User->save();
               
            }else{

              $newstring = substr($request->mobile, -4);

            $User['referral_id']=$request->first_name.'_'.$newstring;

          
            
            $User = User::create($User);
            $data=array("user_id"=>$User->id);
            DB::table('referral_data')->insert($data);
            }

              if(isset($User->id)){

                      $nProvider = User::findOrFail($User->id);
                      $nProvider->otp = $otp;
                      $nProvider->verify_flag=0;
                      $nProvider->save();

            $mobile=$User->mobile;     
            $User['sms'] =send_sms($mobile,$otpsms);    
            $User['otp'] = $otp;
            $User['verify_flag'] = "0";
            }



            return $User;
        } catch (Exception $e) {
             return response()->json(['error' => trans('api.something_went_wrongS')], 500);
        }
    }






             /**
     * Forgot Password.
     *
     * @return \Illuminate\Http\Response
     */


    public function forgot_password(Request $request)
	{
        $this->validate($request, ['email' => 'required|email|exists:users,email',]);
        try{  
            $user = User::where('email' , $request->email)->first();
            $otp=rand(1000,9999);
            $user->otp = $otp;
            $user->save();
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
            //Notification::send($user, new ResetPasswordOTP($otp)); older method to send mail
            return response()->json(['message' => 'OTP sent to your email!','user' => $user]);
        }catch(Exception $e)
		{
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }


    public function reset_password(Request $request)
	{
        $this->validate($request, [
                'password' => 'required|confirmed|min:6',
                'id' => 'required|numeric|exists:users,id'
            ]);
        try{
            $User = User::findOrFail($request->id);
            $User->password = bcrypt($request->password);
            $User->save();
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

    public function change_password(Request $request){

        $this->validate($request, [
                'password' => 'required|confirmed|min:6',
                'old_password' => 'required',
            ]);

        $User = Auth::user();

        if(Hash::check($request->old_password, $User->password))
        {
            $User->password = bcrypt($request->password);
            $User->save();
            if($request->ajax()) {
                return response()->json(['message' => trans('api.user.password_updated')]);
            }else{
                return back()->with('flash_success', 'Password Updated');
            }

        } else {
             if($request->ajax()) {
                return response()->json(['message' => trans('api.user.incorrect_password')]);
            }else{
                return back()->with('flash_error', 'InCorrect Password');
            }
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function update_location(Request $request)
	{
        $this->validate($request, [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

        if($user = User::find(Auth::user()->id)){

            $user->latitude = $request->latitude;
            $user->longitude = $request->longitude;
            $user->save();

            return response()->json(['message' => trans('api.user.location_updated')]);

        }else{

            return response()->json(['error' => trans('api.user.user_not_found')], 500);

        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function details(Request $request){

        $this->validate($request, [
            'device_type' => 'in:android,ios',
        ]);

        try{

            if($user = User::find(Auth::user()->id)){

                if($request->has('device_token')){
                    $user->device_token = $request->device_token;
                }

                if($request->has('device_type')){
                    $user->device_type = $request->device_type;
                }

                if($request->has('device_id')){
                    $user->device_id = $request->device_id;
                }

                $user->save();

                $user->currency = Setting::get('currency');
                $user->picture= 'app/public/'.$user->picture;
                return $user;

            }else{
                return response()->json(['error' => trans('api.user.user_not_found')], 500);
            }
        }
        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function update_profile(Request $request)
    {

        $this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'max:255',
                'email' => 'email|unique:users,email,'.Auth::user()->id,
                'mobile' => 'required',
                'picture' => 'mimes:jpeg,bmp,png',
            ]);

         try {

            $user = User::findOrFail(Auth::user()->id);

            if($request->has('first_name')){ 
                $user->first_name = $request->first_name;
            }
            
            if($request->has('last_name')){
                $user->last_name = $request->last_name;
            }

            if($request->has('mobile')){
                $user->mobile = $request->mobile;
            }
            
            if($request->has('email')){
                $user->email = $request->email;
            }

            if ($request->picture != "") {
                $user->picture = $request->picture->store('user/profile');
            }

            $user->save();

            if($request->ajax()) {
                return response()->json($user);
            }else{
                return back()->with('flash_success', trans('api.user.profile_updated'));
            }
        }

        catch (ModelNotFoundException $e) {
             return response()->json(['error' => trans('api.user.user_not_found')], 500);
        }

    }

    //update_user_otpflag

    public function update_user_otpflag(Request $request)
    {

        
         try {

            $user = User::findOrFail(Auth::user()->id);
                $user->otp = 0;
                $user->verify_flag = 1;
            $user->save();
        
        if($request->ajax()) {
                return response()->json($user);
            }else{
                return back()->with('flash_success', trans('api.user.profile_updated'));
            }
        }

        catch (ModelNotFoundException $e) {
             return response()->json(['error' => trans('api.user.user_not_found')], 500);
        }

    }

    public function sub_services(Request $request){
            
    }
    


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function services() {

        try{
        if($serviceList = ServiceType::all()) {
            foreach($serviceList as $key=>$service){
               $subservices= DB::table('service_sub_types')->where('service_type_id',$service['id'])->get();

                if($subservices->count()>0){
                    foreach($subservices as $skey=>$subService){
                        $serviceList[$key]['sub_services']=$subService;
                    }
                   
                }
            }
            return $serviceList;
        } else {
            return response()->json(['error' => trans('api.services_not_found')], 500);
        }

    }catch(Exception $e){
        return response()->json(['error' => trans($e->getMessage())], 500);
    }
}


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function send_request(Request $request)
	{ 
	
	  date_default_timezone_set('Asia/Kolkata');
      $today = date('m/d/Y');
      $time  = date('H:i');	          
      $mins_later30 =  date('H:i',strtotime($time." +30 Mins"));
	  /*
	  if($request->website == 'website')
      {
		$request->schedule_date = $today;
        $request->schedule_time = $mins_later30;		
	  }	
      */	  
	   $this->validate($request, [
                's_latitude' => 'required|numeric',
                's_longitude' => 'required|numeric',
                'service_type' => 'required|numeric|exists:service_types,id',
                'promo_code' => 'exists:promocodes,promo_code',
                'use_wallet' => 'numeric',
                'payment_mode' => 'required|in:CASH,CARD,PAYPAL',
                'card_id' => ['required_if:payment_mode,CARD','exists:cards,card_id,user_id,'.Auth::user()->id],
            ]);

        Log::info('New Request from user id :'. Auth::user()->id .' params are :');
        Log::info($request->all());

        $ActiveRequests = UserRequests::PendingRequest(Auth::user()->id)->count();

        if($ActiveRequests > 0) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.ride.request_inprogress')], 500);
            }else{
                return redirect('dashboard')->with('flash_error', 'Already request is in progress. Try again later');
            }
        }

        if($request->has('schedule_date') && $request->has('schedule_time')){
  
            if(time() > strtotime($request->schedule_date.$request->schedule_time))
			{

                if($request->ajax()) 
				{
                  return response()->json(['error' => trans('api.ride.request_inprogress')], 500);
                }else{
                    return redirect('dashboard')->with('flash_error', 'Unable to Create Request! Try again later');
                }
            }

            $beforeschedule_time = (new Carbon("$request->schedule_date $request->schedule_time"))->subHour(1);
            $afterschedule_time = (new Carbon("$request->schedule_date $request->schedule_time"))->addHour(1);


        $CheckScheduling = UserRequests::where('status','SCHEDULED')
                            ->where('user_id', Auth::user()->id)
                            ->whereBetween('schedule_at',[$beforeschedule_time,$afterschedule_time])
                            ->get();

            if($CheckScheduling->count() > 0){
                if($request->ajax()) {
                    return response()->json(['error' => trans('api.ride.no_providers_found')], 500);
                }else{
                    return redirect('dashboard')->with('flash_error', 'Already request is Scheduled on this time.');
                }
            }

        }

        $ActiveProviders = ProviderService::AvailableServiceProvider($request->service_type)->get()->pluck('provider_id');
		
		
        Log::alert('message list provider:'.$ActiveProviders);
        $distance = Setting::get('search_radius', '10');
        $latitude = $request->s_latitude;
        $longitude = $request->s_longitude;
		
		
        $provider_id = $request->provider_id;
		if($request->website == 'website') 
		{
	    //echo "<pre>";print_r($ActiveProviders);die;
		 $pr_id = $request->prvdrIdByWeb;
		$Providers = Provider::where('id', $pr_id)
            ->where('status', 'approved')
            ->whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
            ->get();
		}else
		{ 
        $Providers = Provider::where('id', $provider_id)
            ->where('status', 'approved')
            ->whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
            ->get();
		}
//echo "<pre>";print_r($Providers);die;		
        // List Providers who are currently busy and add them to the filter list.

        if(count($Providers) == 0) {
            if($request->ajax()) {
                // Push Notification to User
                return response()->json(['message' => trans('api.ride.no_providers_found')]); 
            }else{
                return back()->with('flash_success', 'No Providers Found! Please try again.');
            }
          // return back()->with('message', 'Some thing went to wrong.');
        }

        try{

            $UserRequest = new UserRequests;
            $UserRequest->booking_id = Helper::generate_booking_id();
            $UserRequest->user_id = Auth::user()->id;
            $UserRequest->current_provider_id = $Providers[0]->id;
            
            $UserRequest->service_type_id = $request->service_type;
            $UserRequest->payment_mode = $request->payment_mode;
            
            $UserRequest->status = 'SEARCHING';

            $UserRequest->s_address = $request->s_address ? : "";

            $UserRequest->s_latitude = $request->s_latitude;
            $UserRequest->s_longitude = $request->s_longitude;

            $UserRequest->use_wallet = $request->use_wallet ? : 0;
            
            $UserRequest->assigned_at = Carbon::now();

            if($request->has('schedule_date') && $request->has('schedule_time')){
                $UserRequest->schedule_at = date("Y-m-d H:i:s",strtotime("$request->schedule_date $request->schedule_time"));
            }

            $UserRequest->save();

            Log::info('New Request id : '. $UserRequest->id .' Assigned to provider : '. $UserRequest->current_provider_id);

            // incoming request push to provider
            (new SendPushNotification)->IncomingRequest($UserRequest->current_provider_id);

            // update payment mode 

            User::where('id',Auth::user()->id)->update(['payment_mode' => $request->payment_mode]);

            if($request->has('card_id')){

                Card::where('user_id',Auth::user()->id)->update(['is_default' => 0]);
                Card::where('card_id',$request->card_id)->update(['is_default' => 1]);
                
            }

                // Send push notifications to the first provider

            (new SendPushNotification)->IncomingRequest($Providers[0]->id);

            foreach ($Providers as $key => $Provider) {

                $Filter = new RequestFilter;
                $Filter->request_id = $UserRequest->id;
                $Filter->provider_id = $Provider->id; 
                $Filter->save();
            }

            if($request->ajax()) {
                return response()->json([
                        'message' => 'New request Created!',
                        'request_id' => $UserRequest->id,
                        'current_provider' => $UserRequest->current_provider_id,
                    ]);
            }else{
                return redirect('dashboard');
            }

        } catch (Exception $e) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }else{
                return back()->with('flash_error', 'Something went wrong while sending request. Please try again.');
            }
        }
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function cancel_request(Request $request) 
	{

        $this->validate($request, [
                'request_id' => 'required|numeric|exists:user_requests,id,user_id,'.Auth::user()->id,
            ]);

        try{

            $UserRequest = UserRequests::findOrFail($request->request_id);

            if($UserRequest->status == 'CANCELLED')
            {
                if($request->ajax()) {
                    return response()->json(['error' => trans('api.ride.already_cancelled')], 500); 
                }else{
                    return back()->with('flash_error', 'Request is Already Cancelled!');
                }
            }

            if(in_array($UserRequest->status, ['SEARCHING','STARTED','ARRIVED','SCHEDULED'])) {

                $UserRequest->status = 'CANCELLED';
                $UserRequest->cancelled_by = 'USER';
                $UserRequest->save();

                RequestFilter::where('request_id', $UserRequest->id)->delete();

                if($UserRequest->status != 'SCHEDULED'){

                    if($UserRequest->provider_id != 0){

                        ProviderService::where('provider_id',$UserRequest->provider_id)->update(['status' => 'active']);

                    }
                }
                (new SendPushNotification)->UserCancellRide($UserRequest);

                if($request->ajax()) {
                    return response()->json(['message' => trans('api.ride.ride_cancelled')]); 
                }else{
                    return redirect('dashboard')->with('flash_success','Request Cancelled Successfully');
                }

            } else {
                if($request->ajax()) {
                    return response()->json(['error' => trans('api.ride.already_onride')], 500); 
                }else{
                    return back()->with('flash_error', 'Service Already Started!');
                }
            }
        }

        catch (ModelNotFoundException $e) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')]);
            }else{
                return back()->with('flash_error', 'No Request Found!');
            }
        }

    }

    public function request_status_check() {
//dd($request->user_id);
        try{

            $check_status = ['CANCELLED','SCHEDULED'];

            $UserRequests = UserRequests::UserRequestStatusCheck(Auth::user()->id,$check_status)
                                        ->get()
                                        ->toArray();
            //$UserRequests->data['0']['tot']=$UserRequests->data['0'];

            $search_status = ['SEARCHING','SCHEDULED'];
            $UserRequestsFilter = UserRequests::UserRequestAssignProvider(Auth::user()->id,$search_status)->get(); 

            $Timeout = Setting::get('provider_select_timeout', 180);

            if(!empty($UserRequestsFilter))
			{
                for ($i=0; $i < sizeof($UserRequestsFilter); $i++) {
                    $ExpiredTime = $Timeout - (time() - strtotime($UserRequestsFilter[$i]->assigned_at));
                    if($UserRequestsFilter[$i]->status == 'SEARCHING' && $ExpiredTime < 0) {
                        $Providertrip = new TripController();
                        $Providertrip->assign_next_provider($UserRequestsFilter[$i]->id);
                    }else if($UserRequestsFilter[$i]->status == 'SEARCHING' && $ExpiredTime > 0){
                        break;
                    }
                }
            }

            return response()->json(['data' => $UserRequests]);

        }

        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }

    } 

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    public function rate_provider(Request $request) {

        $this->validate($request, [
                'request_id' => 'required|integer|exists:user_requests,id,user_id,'.Auth::user()->id,
                'rating' => 'required|integer|in:1,2,3,4,5',
                'comment' => 'max:255',
            ]);
    
        $UserRequests = UserRequests::where('id' ,$request->request_id)
                ->where('status' ,'COMPLETED')
                ->where('paid', 0)
                ->first();
   
        if ($UserRequests) {
            if($request->ajax()){
                return response()->json(['error' => trans('api.user.not_paid')], 500);
            } else {
                return back()->with('flash_error', 'Service Already Started!');
            }
        }

        try{

            $UserRequest = UserRequests::findOrFail($request->request_id);
            
            if($UserRequest->rating == null) {
                UserRequestRating::create([
                        'provider_id' => $UserRequest->provider_id,
                        'user_id' => $UserRequest->user_id,
                        'request_id' => $UserRequest->id,
                        'user_rating' => $request->rating,
                        'user_comment' => $request->comment,
                    ]);
            } else {
                $UserRequest->rating->update([
                        'user_rating' => $request->rating,
                        'user_comment' => $request->comment,
                    ]);
            }

            $UserRequest->user_rated = 1;
            $UserRequest->save();

            $base = UserRequestRating::where('provider_id', $UserRequest->provider_id);
            $average = $base->avg('user_rating');
            $average_count = $base->count();

            $UserRequest->provider->update(['rating' => $average, 'rating_count' => $average_count]);

            // Send Push Notification to Provider 
            if($request->ajax()){
                return response()->json(['message' => trans('api.ride.provider_rated')]); 
            }else{
                return redirect('dashboard')->with('flash_success', 'Provider Rated Successfully!');
            }
        } catch (Exception $e) {
            if($request->ajax()){
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }else{
                return back()->with('flash_error', 'Something went wrong');
            }
        }

    } 


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function trips() {
   
        try{
           // DB::enableQueryLog();

            $UserRequests = UserRequests::UserTrips(Auth::user()->id)->get();
           //dd(DB::getQueryLog());

            if(!empty($UserRequests)){
                $map_icon = asset('asset/marker.png');
                foreach ($UserRequests as $key => $value) {
                    $UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=320x130&maptype=terrian&format=png&visual_refresh=true&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude."&key=".env('GOOGLE_API_KEY');
                }
            }
            return $UserRequests;
			
        }

        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }
    }



    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function trip_details(Request $request) {

         $this->validate($request, [
                'request_id' => 'required|integer|exists:user_requests,id',
            ]);
    
        try{
            $UserRequests = UserRequests::UserTripDetails(Auth::user()->id,$request->request_id)->get();
            if(!empty($UserRequests)){
                $map_icon = asset('asset/marker.png');
                foreach ($UserRequests as $key => $value) {
                    $UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=320x130&maptype=terrian&format=png&visual_refresh=true&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude."&key=".env('GOOGLE_API_KEY');
                }
            }
             foreach($UserRequests as $key=>$value){
                $UserRequests[$key]['before_image']='http://bestservicepoint.com/storage/app/public/'.$value['before_image'];
                $UserRequests[$key]['after_image']='http://bestservicepoint.com/storage/app/public/'.$value['after_image'];
                
            }
            return $UserRequests;
        }

        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }
    }

    /**
     * get all promo code.
     *
     * @return \Illuminate\Http\Response
     */

    public function promocodes() {

        try{

            $this->check_expiry();

            $Promocode = PromocodeUsage::Active()->where('user_id',Auth::user()->id)
                                ->with('promocode')
                                ->get()
                                ->toArray();

            return response()->json($Promocode);

        }

        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }

    } 


    public function check_expiry(){

        try{

            $Promocode = Promocode::all();

            foreach ($Promocode as $index => $promo) {

                if(date("Y-m-d") > $promo->expiration){
                    $promo->status = 'EXPIRED';
                    $promo->save();
                    PromocodeUsage::where('promocode_id',$promo->id)->update(['status' => 'EXPIRED']);
                }

            }

        }    
        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }  
    }


    /**
     * add promo code.
     *
     * @return \Illuminate\Http\Response
     */

    public function add_promocode(Request $request) {

         $this->validate($request, [
                'promocode' => 'required|exists:promocodes,promo_code',
            ]);

        try{

            $find_promo = Promocode::where('promo_code',$request->promocode)->first();

            if($find_promo->status == 'EXPIRED' || (date("Y-m-d") > $find_promo->expiration)){

                if($request->ajax()){

                    return response()->json([
                        'message' => trans('api.promocode_expired'), 
                        'code' => 'promocode_expired'
                    ]);

                }else{
                    return back()->with('flash_error', trans('api.promocode_expired'));
                }

            }elseif(PromocodeUsage::where('promocode_id',$find_promo->id)->where('user_id', Auth::user()->id)->where('status','ADDED')->count() > 0){

                if($request->ajax()){

                    return response()->json([
                        'message' => trans('api.promocode_already_in_use'), 
                        'code' => 'promocode_already_in_use'
                        ]);

                }else{
                    return back()->with('flash_error', 'Promocode Already in use');
                }

            }else{

                $promo = new PromocodeUsage;
                $promo->promocode_id = $find_promo->id;
                $promo->user_id = Auth::user()->id;
                $promo->status = 'ADDED';
                $promo->save();

                if($request->ajax()){

                    return response()->json([
                            'message' => trans('api.promocode_applied') ,
                            'code' => 'promocode_applied'
                         ]); 

                }else{
                    return back()->with('flash_success', trans('api.promocode_applied'));
                }
            }

        }

        catch (Exception $e) {
            if($request->ajax()){
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }else{
                return back()->with('flash_error', 'Something Went Wrong');
            }
        }

    } 

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function upcoming_trips() {
    
        try{
           // DB::enableQueryLog();

            $UserRequests = UserRequests::UserUpcomingTrips(Auth::user()->id)->get();
         //   dd(DB::getQueryLog());

            if(!empty($UserRequests)){
                $map_icon = asset('asset/marker.png');
                foreach ($UserRequests as $key => $value) {
                    $UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=320x130&maptype=terrian&format=png&visual_refresh=true&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude."&key=".env('GOOGLE_MAP_KEY');
                }
            }
            return $UserRequests;
        }

        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function upcoming_trip_details(Request $request) {

         $this->validate($request, [
                'request_id' => 'required|integer|exists:user_requests,id',
            ]);
    
        try{
            $UserRequests = UserRequests::UserUpcomingTripDetails(Auth::user()->id,$request->request_id)->get();
            if(!empty($UserRequests))
			{
                $map_icon = asset('asset/marker.png');
                foreach ($UserRequests as $key => $value) 
				{
                    $UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=320x130&maptype=terrian&format=png&visual_refresh=true&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude."&key=".env('GOOGLE_MAP_KEY');
                }
            }
            return $UserRequests;
        }

        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }
    }


        /**
     * Show the nearby providers.
     *
     * @return \Illuminate\Http\Response
     */

    public function show_providers(Request $request) {

        $this->validate($request, [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'service' => 'required|numeric|exists:service_types,id',
            ]);

        try{

            $ActiveProviders = ProviderService::AvailableServiceProvider($request->service)->get()->pluck('provider_id');

            $distance = Setting::get('search_radius', '10');
            $latitude = $request->latitude;
            $longitude = $request->longitude;

            $Providers = Provider::whereIn('id', $ActiveProviders)
                ->where('status', 'approved')
                ->whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
                ->get();

            if(count($Providers) == 0) {
                if($request->ajax()) {
                    return response()->json(['message' => "No Providers Found"]); 
                }else{
                    return back()->with('flash_success', 'No Providers Found! Please try again.');
                }
            }
        
            return $Providers;

        } catch (Exception $e) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            }else{
                return back()->with('flash_error', 'Something went wrong while sending request. Please try again.');
            }
        }
    }



    /**
     * Show the provider.
     *
     * @return \Illuminate\Http\Response
     */

    public function provider(Request $request) {
dd();
        $this->validate($request, [
                'provider_id' => 'required|numeric|exists:providers,id',
            ]);

        if($Provider = Provider::find($request->provider_id)) {

            if($Services = ServiceType::all()) {
                foreach ($Services as $key => $value) {
                    $price = ProviderService::where('provider_id',$request->provider_id)
                            ->where('service_type_id',$value->id)
                            ->first();
                    if($price){
                        $Services[$key]->available = true;
                    }else{
                        $Services[$key]->available = false;
                    }
                }
            } 


            return response()->json([
                    'provider' => $Provider, 
                    'services' => $Services,
                ]);

        } else {
            return response()->json(['error' => 'No Provider Found!'], 500);
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
            User::where('id', $request->id)->update(['device_id'=> '', 'device_token' => '']);
            return response()->json(['message' => trans('api.logout_success')]);
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }

    
    /**
     * help Details.
     *
     * @return \Illuminate\Http\Response
     */

    public function help_details(Request $request)
	{

        try{

            if($request->ajax()) {
                return response()->json([
                        'contact_number' => Setting::get('contact_number',''), 
                        'contact_email'  => Setting::get('contact_email',''),
                        'contact_text'   => Setting::get('contact_text',''),
                        'contact_title'  => Setting::get('site_title',''),
                     ]);
            }

        }catch (Exception $e) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')]);
            }
        }
    }

public function services2() {
dd(114324);
        if($serviceList = ServiceType::all()) {
            return $serviceList;
        } else {
            return response()->json(['error' => trans('api.services_not_found')], 500);
        }

    }


}
