<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Setting;
use DB;
use Auth;
use App\User;
use Validator;
use Illuminate\Contracts\Auth\Authenticatable;



class HomeController extends Controller
{
    protected $UserAPI;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserApiController $UserAPI)
    {
        $this->middleware('auth');
        $this->UserAPI = $UserAPI;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

       if(Auth::user()->mobile==null || Auth::user()->mobile==''|| is_numeric(Auth::user()->mobile)==''  ){
            return redirect()->to('/mobilenumbersubmit'); 
          // return redirect()->to('/profile'); 
          // return redirect()->to('/profile'); 
       }

       if(Auth::user()->verify_flag==0 ){
           $this->fire_otp();
           return redirect()->to('/otp'); 
       }

        $Response = $this->UserAPI->request_status_check()->getData();
        if(empty($Response->data))
        {
            if($request->has('service')){
                $cards = (new Resource\CardResource)->index();
				
                $service = (new Resource\ServiceResource)->show($request->service);
                return view('user.request',compact('cards','service'));
            }else{
                $services = $this->UserAPI->services();
         
                return view('user.dashboard',compact('services'));
            }
        }else{
            return view('user.ride.waiting')->with('request',$Response->data[0]);
        }
    }
	
public function fire_otp(){
           //$user = User::where('id' , Auth::user()->id);
             $user = DB::table('users')->where('id' , Auth::user()->id)->first();
           if(isset($user->id)){ 
              $mobile =$user->mobile;
          
              $otp=rand(1000,9999); 
              $otpsms=" Your Otp is: ".$otp;
              send_sms($mobile,$otpsms);
              DB::table('users')
                ->where('id', $user->id)
                ->update(['otp' => $otp , 'verify_flag'=> 0 ]);           
            } 
   
}

public function otp()
{
        if( Auth::user()->verify_flag==1 ){
           return redirect()->to('/dashboard'); 
        }
        $user = DB::table('users')->where('id', Auth::user()->id)->first();
        $mobile =Auth::user()->mobile;
        return view('user.auth.otp', ['mobile' =>$mobile]);
}


public function mobilenumbersubmit(Request $request)
{
    if(Auth::user()->id){    
        if(  Auth::user()->mobile!='' || Auth::user()->verify_flag==1 ){
           return redirect()->to('/dashboard'); 
        }
        $user = DB::table('users')->where('id', Auth::user()->id)->first();
        $email =$user->email;
       
        
         //$this->validate($request, [
          //       'email' => 'required',
         //        'mobile' => 'required|numeric|min:10',
        //     ]);
        // $userr = DB::table('users')->where('id', Auth::user()->id)->first();
        // if($userr){
        //     $userr->mobile = $request->mobile;
        //     $userr->verify_flag = 0;
        //     $userr->save();
        //     $this->otp_fire();
        //  return redirect()->to('/otp'); 
        // }
        return view('user.auth.tomobile_field_form', ['email' =>$email]);
    }else{
        return redirect()->to('/logout'); 
    }    
}

public function mobilenumbersubmited(Request $request)
{
    if(Auth::user()->id){    
        if(  Auth::user()->mobile!='' || Auth::user()->verify_flag==1 ){
           return redirect()->to('/dashboard'); 
        }
        $user = DB::table('users')->where('id', Auth::user()->id)->first();
        $email =$user->email;
       
        
         //$this->validate($request, [
          //       'email' => 'required',
         //        'mobile' => 'required|numeric|min:10',
        //     ]);
         $userr = DB::table('users')->where('id', Auth::user()->id)->first();
         if($userr){
             //$userr->mobile = $request->mobile;
             //$userr->verify_flag = 0;
             DB::table('users')
                ->where('id', $userr->id)
                ->update(['mobile' => $request->mobile , 'verify_flag'=> 0 ]);           
            //$userr->save();
            $this->fire_otp();
          return redirect()->to('/otp'); 
         }
      
        return redirect()->to('/mobilenumbersubmit'); 
   
    }else{
        return redirect()->to('/logout'); 
    }    
}


public function otpSubmit(Request $request)
{
            $otp_code=$request->otp;
            $user = DB::table('users')->where('id' , Auth::user()->id)->where( 'otp',$otp_code )->first();
            if(isset($user->id)){    
               $this->UserAPI->update_user_otpflag($request);
                 $user = DB::table('users')->where('id' , Auth::user()->id)->first();
                 Auth::user()->mobile=$user->mobile;
                 Auth::user()->verify_flag=1;
                   return redirect('/dashboard')->with('flash_success', 'Your account Varify Successfully .');
         
            }else{
            // return back()->with('flash_error', 'Disabled for demo purposes! Please contact us at info@dragon.com');
            return redirect('/otp')->with('flash_error', 'Please Enter Valid Otp .');
            }
}


public function resendotp()
{

    if(Auth::user()->mobile==null || Auth::user()->mobile==''|| is_numeric(Auth::user()->mobile)==''  ){
           return redirect()->to('/profile'); 
          // return redirect()->to('/profile'); 
       }

       if(Auth::user()->verify_flag==1  || Auth::user()->mobile!=''){
           return redirect()->to('/dashboard'); 
          // return redirect()->to('/profile'); 
       }

       if(Auth::user()->verify_flag==0  || Auth::user()->mobile!=''){
            $this->fire_otp();
       
            return back()->with('flash_success', 'Otp send Successfully');
            //return redirect()->to('/otp'); 
          // return redirect()->to('/profile'); 

       }


}

    public function index2(Request $request)
    { 
	   //echo "<pre>";print_r($request->all());die;
	   $s_address  = $request['s_address']; 
	   $latitude   = $request['s_latitude'];
 	   $longitude  = $request['s_longitude'];
 	   $service_id = $request['service_type'];
	   $schedule_time = $request['schedule_time'];
	   $schedule_date = $request['schedule_date'];
	   
	   $url = 'http://www.bestservicepoint.com/storage/app/public/';
       $sql  ="select
       (((acos(sin(('$latitude'*pi()/180)) * sin((`latitude`*pi()/180))+cos(('$latitude'*pi()/180)) 
       * cos((`latitude`*pi()/180)) * cos((('$longitude'- `longitude`)*pi()/180))))*180/pi())*60*1.1515) 
       AS  distance ,providers.id,providers.first_name,providers.uniq_no,providers.last_name,providers.email,providers.rating,providers.exp,providers.description,providers.w_start,
       providers.mobile,providers.avatar,
       provider_services.service_type_id,provider_services.service_ki_price
      ,ifnull(concat('$url',providers.avatar),0) as image_of, providers.provider_url
       from providers 
       join  provider_services on providers.id = provider_services.provider_id	
       where provider_services.service_type_id = '$service_id'			  
        having distance <= 15 order  by  distance ASC";

       $query = DB::select($sql);
   if(count($query) > 0)
     { 
     $custom=array();		
     foreach ($query as $key) {
         $key->distance=$key->distance*2.1;	# code...
          array_push($custom, $key);          
         }	
           
			 // echo "<pre>";print_r($query);die;
		      return view('user/request2',['service_id'=> $service_id,'data'=>$custom,
			                              'latitude'=>$latitude,'longitude'=>$longitude,
										  's_address' => $s_address,'schedule_time'=>$schedule_time,'schedule_date'=>$schedule_date]);
    }}	
	
	
public function sig()
{
	echo "ok";die;
}
    /**
     * Show the application profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {

       if(Auth::user()->mobile==null || Auth::user()->mobile==''|| is_numeric(Auth::user()->mobile)==''  )
        {
        return view('user.account.profile');
        }      

       if(Auth::user()->verify_flag==0 ){
           return redirect()->to('/otp'); 
          // return redirect()->to('/profile'); 
       }
    return view('user.account.profile');
       
    }

    /**
     * Show the application profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit_profile()
    {
        return view('user.account.edit_profile');
    }

    /**
     * Update profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function update_profile(Request $request)
    {

        if(Setting::get('demo_mode', 0) == 1) {
            return back()->with('flash_error', 'Disabled for demo purposes! Please contact us at info@dragon.com');
        }

        return $this->UserAPI->update_profile($request);
    }

    /**
     * Show the application change password.
     *
     * @return \Illuminate\Http\Response
     */
    public function change_password()
    {
        
        if(Auth::user()->mobile==null || Auth::user()->mobile==''|| is_numeric(Auth::user()->mobile)==''  )
        {
        return view('user.account.profile');
        }      

       if(Auth::user()->verify_flag==0 ){
           return redirect()->to('/otp'); 
          // return redirect()->to('/profile'); 
       }

        return view('user.account.change_password');
    }

    /**
     * Change Password.
     *
     * @return \Illuminate\Http\Response
     */
    public function update_password(Request $request)
    {
        if(Setting::get('demo_mode', 0) == 1) {
            return back()->with('flash_error', 'Disabled for demo purposes! Please contact us at info@dragon.com');
        }
        
        return $this->UserAPI->change_password($request);
    }

    /**
     * Trips.
     *
     * @return \Illuminate\Http\Response
     */
    public function trips()
    {
        $provider_id = Auth::user()->id;
        $sql = "SELECT ur.*,u.first_name,u.last_name,u.email ,st.name as service_name  FROM user_requests ur 
		         JOIN providers u ON ur.user_id = u.id JOIN service_types st on ur.service_type_id= st.id
	             where ur.user_id = '$provider_id' AND ur.status != 'SCHEDULED'";
	
	    $query = DB::select($sql);

		//$trips = $this->UserAPI->trips();   # THIS IS OLD CODE TO DISPLAY MY TRIP AS USER
		 //return view('user.ride.trips',compact('trips'));
		//echo "<pre>";print_r($query);die;
		return view('user.ride.trips222',['fully' => $query]);
       
    }
    
        public function my_request()
    {

        if(Auth::user()->mobile==null || Auth::user()->mobile==''|| is_numeric(Auth::user()->mobile)==''  )
        {
        return view('user.account.profile');
        }      

       if(Auth::user()->verify_flag==0 ){
           return redirect()->to('/otp'); 
          // return redirect()->to('/profile'); 
       }

        $provider_id = Auth::user()->id;
        $sql = "SELECT ur.*,u.first_name,u.last_name,u.email ,st.name as service_name  FROM user_requests ur 
		         JOIN users u ON ur.user_id = u.id JOIN service_types st on ur.service_type_id= st.id
	             where ur.user_id = '$provider_id' AND ur.status != 'SCHEDULED'";
	 
	    $query = DB::select($sql);

		//$trips = $this->UserAPI->trips();   # THIS IS OLD CODE TO DISPLAY MY TRIP AS USER
		 //return view('user.ride.trips',compact('trips'));
	//echo "<pre>";print_r($query);die;
		return view('user.ride.trips222',['fully' => $query]);
       
    }

     /**
     * Payment.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment()
    {

         if(Auth::user()->mobile==null || Auth::user()->mobile==''|| is_numeric(Auth::user()->mobile)==''  )
        {
        return view('user.account.profile');
        }      

       if(Auth::user()->verify_flag==0 ){
           return redirect()->to('/otp'); 
          // return redirect()->to('/profile'); 
       }

        $cards = (new Resource\CardResource)->index();
        return view('user.account.payment',compact('cards'));
    }

        public function help()
    {
       
        return view('user.help');
    }

    /**
     * Wallet.
     *
     * @return \Illuminate\Http\Response
     */
    public function wallet(Request $request)
    {

         if(Auth::user()->mobile==null || Auth::user()->mobile==''|| is_numeric(Auth::user()->mobile)==''  )
        {
        return view('user.account.profile');
        }      

       if(Auth::user()->verify_flag==0 ){
           return redirect()->to('/otp'); 
          // return redirect()->to('/profile'); 
       }

        $cards = (new Resource\CardResource)->index();

       
        return view('user.account.wallet',compact('cards'));
    }

    public function loadwallet(Request $request)
    {

        if(Auth::user()->mobile==null || Auth::user()->mobile==''|| is_numeric(Auth::user()->mobile)==''  )
        {
        return view('user.account.profile');
        }      

       if(Auth::user()->verify_flag==0 ){
           return redirect()->to('/otp'); 
          // return redirect()->to('/profile'); 
       }
       $amount= $request->amount ;
    

       return view('user.account.loadwallet',compact('amount'));
    }

    /**
     * Promotion.
     *
     * @return \Illuminate\Http\Response
     */
    public function promotion(Request $request)
    {

         if(Auth::user()->mobile==null || Auth::user()->mobile==''|| is_numeric(Auth::user()->mobile)==''  )
        {
        return view('user.account.profile');
        }      

       if(Auth::user()->verify_flag==0 ){
           return redirect()->to('/otp'); 
          // return redirect()->to('/profile'); 
       }


        $promocodes = $this->UserAPI->promocodes()->getData();
        return view('user.account.promotion',compact('promocodes'));
    }

    /**
     * Add promocode.
     *
     * @return \Illuminate\Http\Response
     */
    public function add_promocode(Request $request)
    {
        return $this->UserAPI->add_promocode($request);
    }

    /**
     * Upcoming Trips.
     *
     * @return \Illuminate\Http\Response
     */
    public function upcoming_trips()
    {

         if(Auth::user()->mobile==null || Auth::user()->mobile==''|| is_numeric(Auth::user()->mobile)==''  )
        {
        return view('user.account.profile');
        }      

       if(Auth::user()->verify_flag==0 ){
           return redirect()->to('/otp'); 
          // return redirect()->to('/profile'); 
       }

        $trips = $this->UserAPI->upcoming_trips();
        return view('user.ride.upcoming',compact('trips'));
    }

}
