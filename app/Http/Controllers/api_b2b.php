<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use DB;
use Session;
use Mail;
use App\User;
use App\Http\Controllers\SendPushNotification;
use Auth;
use Setting;
use Storage;
use Carbon\Carbon;


use App\Helpers\Helper;
use App\RequestFilter;
use App\UserRequests;
use App\ProviderService;
use App\PromocodeUsage;
use App\Provider;
use App\Promocode;
use App\UserRequestRating;
use App\UserRequestPayment;

class api_b2b extends BaseController
{  
    public $var_one ="";
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	

	    public function thsi_wrong(Request $request)
    {
        try{

//echo "<pre>";print_r($Provider);die;
           $provider   = $request->id;
           $request_id = $request->request_id;


            $AfterAssignProvider = RequestFilter::with(['request.user', 'request.payment', 'request' ,'request.service_type'])
                ->where('provider_id', $provider)
                ->whereHas('request', function($query) use ($provider) {
                        $query->where('status','<>', 'CANCELLED');
                        $query->where('status','<>', 'SCHEDULED');
                        $query->where('provider_id', $provider );
                        $query->where('current_provider_id', $provider);
                    });

            $BeforeAssignProvider = RequestFilter::with(['request.user', 'request.payment', 'request','request.service_type'])
                ->where('provider_id', $provider)
                ->whereHas('request', function($query) use ($provider){
                        $query->where('status','<>', 'CANCELLED');
                        $query->where('status','<>', 'SCHEDULED');
                        $query->where('current_provider_id',$provider);
                    });

            $IncomingRequests = $BeforeAssignProvider->union($AfterAssignProvider)->get();


             /*
			 if(!empty($request->latitude)) {
                $Provider->update([
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                ]);
            }
              */
            $Timeout = Setting::get('provider_select_timeout', 180);
                if(!empty($IncomingRequests))
				{
                    for ($i=0; $i < sizeof($IncomingRequests); $i++) 
					{
                        $IncomingRequests[$i]->time_left_to_respond = $Timeout - (time() - strtotime($IncomingRequests[$i]->request->assigned_at));
                        if($IncomingRequests[$i]->request->status == 'SEARCHING' && $IncomingRequests[$i]->time_left_to_respond < 0)
							{
                            $this->assign_next_provider($IncomingRequests[$i]->request->id);
                        }
                    }
                }
  
            $Response = [
                    'status' => 'active',
                    'service_status' => 'started',//$Provider->service ? Auth::user()->service->status : 'offline',
                    'requests'       => $IncomingRequests,
                ];

            return $Response;

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Something went wrong']);
        }
    }
	
	
	
	
	
	
	
	
	public function sig2(Request $request)
	{
       $con = mysqli_connect("localhost","swxzcxwujy","swxzcxwujy","swxzcxwujy");
	   
	}
	



	
	public function sig(Request $request)
	{ 
//echo 'ok';die;
		       $sql="select u.*, oat.id as access_token,ort.id as refresh_token   from users u join oauth_access_tokens oat on u.id = oat.user_id 
				               join oauth_refresh_tokens ort on oat.id = ort.access_token_id 
							    where u.email ='".$request['email']."'   
			                 ";
                $query = DB::select($sql);
				
                if(count($query) > 0)
                {
					$query[0]->token_type	= "Bearer";	
				 return response()->json(['success' => 200,'result'=>true,'message' => "Successfully Login" ,"data" => $query]);
				}
             else{				
			    $arr_data =array
				(
		        'first_name'=>$request['first_name'],
				'email'=>$request['email'],
				'mobile'=>$request['mobile'],
				'last_name'=>$request['first_name'],
				'payment_mode'=>"cash",
				'password'=>"123",
				'picture'=>"jsdljkflsjdfj",
				'login_by'=>"facebook",
				'wallet_balance'=> 0,
				'rating'=>5,
				'rating_count'=>0,
				'otp'=>0,
				
				);
				//print_r($arr_data);die;
    			$last_id =DB::table('users')->insertGetId($arr_data);
				
			    $oauth_access_tokens= md5(uniqid($last_id, true));
				$arr2 = array('id'=>$oauth_access_tokens,'user_id'=>$last_id,'client_id'=>2,'revoked'=>0);
				
				DB::table('oauth_access_tokens')->insert($arr2);	  
                
				$rand = rand(1000,10000);
			    $oauth_refresh_tokens= md5(uniqid($last_id, true));
				$arr3 = array('id'=>$oauth_refresh_tokens ,'access_token_id'=>$oauth_access_tokens,'revoked'=>0,);
				DB::table('oauth_refresh_tokens')->insert($arr3);                				
		     	

                $sq= "select u.*, oat.id as access_token,ort.id as refresh_token
        				from users u join oauth_access_tokens oat on u.id = oat.user_id 
				               join oauth_refresh_tokens ort on oat.id = ort.access_token_id 
							   where u.id='$last_id'";
                $query95 = DB::select($sq);
                $query95[0]->token_type	= "Bearer";			
				if(isset($last_id))
				{
	            return response()->json(['success' => 200,'result'=>true,'message' => "Successfully Login" ,"data" => $query95]);
				}
				else
				{
            	return response()->json(['success' => 500,'result'=>false,'message' => "Not Login" ]);			
				}
		 }
    }
	
	
	
	
	
	
	
		public function refresh_token(Request $request)
	{ 

		       $sql ="select oat.id as access_token,ort.id as refresh_token   from users u join oauth_access_tokens oat on u.id = oat.user_id 
				               join oauth_refresh_tokens ort on oat.id = ort.access_token_id 
							    where u.email ='".$request['email']."'   
			                 ";
                $query = DB::select($sql);
				
                if(count($query) > 0)
                {
					$query[0]->token_type	= "Bearer";	
				 return response()->json(['success' => 200,'result'=>true,'message' => "Successfully Login" ,"data" => $query]);
				}
	}			
	
	
	
	
	
	
	
	
	
	
	public function check_provider_email(Request $request)
	{ 

	   $sql="select * from providers where email ='".$request['email']."'";
		$query = DB::select($sql);
		if(count($query) > 0)
		{
		 
		 return response()->json(['status'=>"1"]);
		}
	 else
	 {				

		return response()->json(['status'=>"0" ]);			
		}
	}
	
		public function check_user_email(Request $request)
	{ 

	   $sql="select * from users where email ='".$request['email']."'";
		$query = DB::select($sql);
		if(count($query) > 0)
		{
		 
		 return response()->json(['status'=>"1"]);
		}
	 else
	 {				

		return response()->json(['status'=>"0" ]);			
		}
	}
    
	
    public function forget_p(Request $request)
	{ 


	   $sql="select * from users where email ='".$request['email']."'";
		$query = DB::select($sql);
		if(count($query) > 0)
		{ //ECHO "OK";DIE;
/*	
$otp = rand(10000,100000);
$sender = 'mohdsalman626@gmail.com';
$recipient = $request['email'];
$subject = "php mail test";
$message = "Your otp is ".$otp;
$headers = 'From:' . $sender;
if (mail($recipient, $subject, $message, $headers))
{	//echo "ys";
}

*/

$otp = rand(10000,100000);
$to_name = 'ro';
$to_email = $request['email'];
$last_id_booking = 0;
$data  = 'OK';
$data = array('name'=>"Ogbonna Vitalis(sender_name)","id"=>$otp, "body" => "",'response'=>$data);
Mail::send('email', ['data'=>$data], function($message) use ($to_name, $to_email) {
$message->to($to_email, $to_name)
->subject("Best Service Forget Password");
$message->from('mohdsalman626@gmail.com'," Best Service");
});


		  $sql="update  users set otp = '$otp' where email ='".$request['email']."'";
		  $query = DB::select($sql);
		 return response()->json(['status'=>"1"]);
		}
	 else
	 {				

		return response()->json(['status'=>"0" ]);			
		}
	}
	
	
	
	
	
	
	
	
	
	
	 public function check_user_otp(Request $request)	
	{ 
	    $sql="select * from users where email ='".$request['email']."' AND otp ='".$request['otp']."'";
		$query = DB::select($sql);
		if(count($query) > 0)
		{
		 $user = User::where('email' , $request->email)->first();
		 return response()->json(['status'=>"1",'data'=>$user]);
		}
	    else
	   {				

		return response()->json(['status'=>"0" ]);			
		}
	}
	
 public function add_money_to_my_account(Request $request)	
{ 
	$sql="select id,wallet_balance from users where id ='".$request['user_id']."'";
	$query = DB::select($sql);
	if(count($query) > 0)
	{
	 //print_r($query);die;	
	 $wallet_balance = 	$query[0]->wallet_balance;
	 $sql2           =  "UPDATE users set wallet_balance = '$wallet_balance'+'".$request['amount']."' where id = '$request->user_id' ";
	 $query          =  DB::select($sql2);
	 $user           =  User::where('id' , $request->user_id)->first();
	 return response()->json(['status'=>true,'status_code'=>200,'message'=>$user]);

	}
	else
   {				
	 return response()->json(['status'=>false,'status_code'=>500,'message'=>'Sorry Something went wrong']);			
   }
}
	
	
	

public function get_nearby_provider(Request $request)
{	//dd($request);
  
    $lat=$latitude   = $request['latitude'];
 	$long=$longitude  = $request['longitude'];
 	$service_id = $request['service_id'];
$url = 'http://www.bestservicepoint.com/storage/app/public/';
          
 /*$sql= "select (3956 * 2 * ASIN(SQRT( POWER(SIN(( $lat - providers.latitude) *  pi()/180 / 2), 2) +COS( $lat * pi()/180) * COS(providers.latitude * pi()/180) * POWER(SIN(( $long - providers.longitude) * pi()/180 / 2), 2) ))) as distance ,providers.id,providers.first_name,providers.last_name,providers.email,providers.rating,providers.exp,providers.description,providers.w_start,
			  providers.mobile,providers.avatar,
			  provider_services.service_type_id,provider_services.service_ki_price
			 ,ifnull(concat('$url',providers.avatar),0) as image_of
			  from providers 
             left join  provider_services on providers.id = provider_services.provider_id	
              where provider_services.service_type_id = '$service_id'			  
               having  distance <= 15 
      order by distance"; */
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

          /*   $sql  ="select
              (((acos(sin(('$latitude'*pi()/180)) * sin((`latitude`*pi()/180))+cos(('$latitude'*pi()/180)) 
              * cos((`latitude`*pi()/180)) * cos((('$longitude'- `longitude`)*pi()/180))))*180/pi())*60*1.1515) 
              AS  distance ,providers.id,providers.first_name,providers.last_name,providers.email,providers.rating,providers.exp,providers.description,providers.w_start,
			  providers.mobile,providers.avatar,
			  provider_services.service_type_id,provider_services.service_ki_price
			 ,ifnull(concat('$url',providers.avatar),0) as image_of
			  from providers 
              join  provider_services on providers.id = provider_services.provider_id	
              where provider_services.service_type_id = '$service_id'			  
               having distance < 15 order  by  distance ASC
              ";*/
   //
//echo $sql ;//die;
              $query = DB::select($sql);
          if(count($query) > 0)
            { 
            $custom=array();		
            foreach ($query as $key) {
            	$key->distance=$key->distance*2.1;	# code...
            	 array_push($custom, $key);          
            	}	
              return response()->json(['success' => 200,'result'=>true,'image_url'=>$url,'cus_promo_wallet'=>0,'message'=>$custom]);	    
    	    }
         else
          {
    		 //return response()->json(['success' =>500,'result'=>false,'message' => "Check your credentials"]); 
    		 return response()->json(['success' =>201,'result'=>false,'message' => "Provider Not Found"]); 
    	   } 	
}
	
	
	public function insert_slider(Request $request)
	{
		 if($request->hasfile('slider_icon'))
         {
          $image = $request->file('slider_icon');	
		  $name_of_img=$image->getClientOriginalName();  
		  $image->move(public_path().'/storage/service/', $name_of_img);   
		  $ari =array(
		      'title'       => $request['title'], 
		      'description' => $request['description'],
		      'slider_icon' => $name_of_img , 
              'user_type'   => $request['user_type'],			  
		  );
                 //print_r($ari);die;       
                DB::table('slider')->insert($ari);	
            return redirect('all_slider'); 				
            
         }	
	}
	
		public function delete_slider($id) 
	{  

           $group_id= $id; 
	    DB::table('slider')
            ->where('slider_id', $group_id)
            ->delete();
	     return redirect('all_slider');
	   

	}
	
	
	 public function edit_slider($id)
    { 
		$group_id = $id; 
		$data =DB::table('slider')->where(array('slider_id'=> $group_id))->get();
	 	//print_r($data);die;
		return view('admin/users/edit_slider',['data'=>$data]);
    }
	
	  	public function update_slider(Request $request)
	{
	     $slider_id    = $request['slider_id'];
	     $title        = $request['title'];
	     $description  = $request['description'];
		 $user_type    = $request['user_type'];
		 

    	 if($request->hasfile('slider_icon'))
         {
			   $image = $request->file('slider_icon'); 
			   $catagory_icon=$image->getClientOriginalName();
			   $image->move(public_path().'/storage/service/', $catagory_icon);
			   DB::table('slider')
			   ->where('slider_id', $slider_id)
			   ->update(
			   array('title'=>$title,'description'=>$description,'user_type'=>$user_type,'slider_icon'=> $catagory_icon));
			   return redirect()->back()->with('message', 'Update Successfully!');    
          }
           DB::table('slider')
           ->where('slider_id', $slider_id)
           ->update(
           array('title'=>$title,'description'=>$description,'user_type'=>$user_type));
           return redirect()->back()->with('message', 'Update Successfully!');
	}

	
	
		 public function get_slider_for_user()	
	{   
		//echo base_url(); dd();
	    $url = 'http://bestservicepoint.com/storage/service/';
	    $sql="select slider.*,concat('$url',slider_icon) as image from slider where user_type = 'USER'";
		$query = DB::select($sql);
		if(count($query) > 0)
		{
		 
		 return response()->json(['status'=>"1",'data'=>$query]);
		}
	    else
	   {				

		return response()->json(['status'=>"0" ]);			
		}
	}
	
	
    public function get_slider_for_provider()	
	{   
	    $url = 'http://165.22.220.242/storage/service/';
	    $sql="select slider.*,concat('$url',slider_icon) as image from slider where user_type = 'PROVIDER'";
		$query = DB::select($sql);
		if(count($query) > 0)
		{
		 
		 return response()->json(['status'=>"1",'data'=>$query]);
		}
	    else
	   {				

		return response()->json(['status'=>"0" ]);			
		}
	}
	

function check_outs($request_id,$provider_id){
    
   // dd($request['request_id']);
    
    date_default_timezone_set('Asia/Kolkata');
    $today   = date('Y-m-d');
    $time  = date('Y-m-d H:i:s');
	$req_id = $request['request_id'];
		$sql="select * from user_requests where id ='".$request['request_id']."'";
		$query = DB::select($sql);
		if(count($query) > 0)
		{ 
	//$req_id = $id;
$sql="select u.mobile,u.device_token from user_requests ru join users u on ru.user_id = u.id where ru.id ='$req_id'";
$query = DB::select($sql);
$mobile = $query[0]->mobile;
$message= urlencode('BEST SERVICE: Dear user your request has been accepted by service provider');
# CODE TO SEND NOTIFICATION TO User

	$msg1              = 'Hey your service has been started';

	 $token = $query[0]->device_token; 
	 define( 'API_ACCESS_KEY', 'AAAAKKkZISA:APA91bG_-D3f8weluVLBZTd052iGDu6i-LXPsqM_Ikv8rO2cuS1VnTcfDontEUxdAzC2FNexYIVv4iBMC1H1wRCRywFxdVCZ8TNOtfCdolpYdHEZtjDcG82KWSvB_bU-ZCTxcSqz-BqQ');
	 $msg = array
        (
            'body' => $msg1,
            'title' => 'Best Service Information',
              );
    $fields = array
    (
    'to' => $token,
    'notification' => $msg
    );
    $headers = array
    (
    'Authorization: key=' . API_ACCESS_KEY,
    'Content-Type: application/json'
    );
    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    $result = curl_exec($ch );
	
    //echo $result;die;
    curl_close( $ch ); 
	
	
	
		 $sql="update  user_requests set status = 'STARTED',provider_id ='".$request['provider_id']."',
                current_provider_id ='".$request['provider_id']."',
				started_at = '$time'
				where id ='".$request['request_id']."'";
		  $query = DB::select($sql);
		 return response()->json(['status'=>"1",'mesaage'=>'Successfully started']);
		}
	  else
	 {	
		return response()->json(['status'=>"0" ,'mesaage'=>'something went wrong']);			
	 }
}



    public function check_out(Request $request)
	{ 
      
       if($request['request_id']==0 || $request['request_id']==''|| $request['request_id']==null) {
           return response()->json(['status'=>"0" ,'mesaage'=>'something went wrong']);	
       }
          
          
        if($request['provider_id']==null || $request['provider_id']=='' || $request['provider_id']==0){
           	return response()->json(['status'=>"0" ,'mesaage'=>'something went wrong']);		
       }
       
	date_default_timezone_set('Asia/Kolkata');
    $today   = date('Y-m-d');
    $time  = date('Y-m-d H:i:s');
	$req_id = $request['request_id'];
		$sql="select * from user_requests where id ='".$request['request_id']."'";
		$query = DB::select($sql);
		if(count($query) > 0)
		{ 
	//$req_id = $id;
$sql="select u.mobile,u.device_token from user_requests ru join users u on ru.user_id = u.id where ru.id ='$req_id'";
$query = DB::select($sql);
$mobile = $query[0]->mobile;
$message= urlencode('BEST SERVICE: Dear user your request has been accepted by service provider');
# CODE TO SEND NOTIFICATION TO User


           $UserRequest = UserRequests::findOrFail($request['request_id']);
        
              (new SendPushNotification)->user_schedule($UserRequest);
	  
	
	
		 $sql="update  user_requests set status = 'STARTED',provider_id ='".$request['provider_id']."',
                current_provider_id ='".$request['provider_id']."',
				started_at = '$time'
				where id ='".$request['request_id']."'";
		  $query = DB::select($sql);
		 return response()->json(['status'=>"1",'mesaage'=>'Successfully started']);
		}
	  else
	 {	
		return response()->json(['status'=>"0" ,'mesaage'=>'something went wrong']);			
	 }
	}
	

    public function update_service_status(Request $request)
	{ 
	 $com = $request->all();
	 
	date_default_timezone_set('Asia/Kolkata');
    $today   = date('Y-m-d');
    $time  = date('Y-m-d H:i:s');
	$req_id = $request['id'];
    $status = $request['status'];
	
    	 if($request->hasfile('bef_image'))
         {
		   $image = $request->file('bef_image'); 
		   $catagory_icon=$image->getClientOriginalName();
		   $image->move(public_path().'/storage/service/', $catagory_icon);
		   if($request['tag'] == 'before')
		   {	   
		    DB::table('user_requests')
		        ->where('id', $req_id)
		        ->update(
		    array('img1'=> "service/".$catagory_icon,'before_image'=> "service/".$catagory_icon));
		   }else
		   {
			DB::table('user_requests')
		        ->where('id', $req_id)
		        ->update(
		    array('img2'=> "service/".$catagory_icon,'after_image'=> "service/".$catagory_icon));   
		   }
		 }  

	
		 $sql="update  user_requests set status = '$status',
				started_at = '$time'
				where id ='".$request['id']."'";
		  $query = DB::select($sql);
		  return redirect('provider/upcoming222')->with('success', 'Data saved successfully!');

	}



    public function update_service_statusBYuser(Request $request)
	{ 
	 $com = $request->all();
	 
	date_default_timezone_set('Asia/Kolkata');
    $today   = date('Y-m-d');
    $time  = date('Y-m-d H:i:s');
	$req_id = $request['id'];
    $status = $request['status'];

		 $sql="update  user_requests set status = '$status'
				where id ='".$request['id']."'";
		  $query = DB::select($sql);
		  return redirect()->back()->with('success', 'Data saved successfully!');

	}












	 public function price_of_provider(Request $request)	
	{ 
	 $query = array();
	 $sql0 = "SELECT id,name FROM service_types where name ='".$request['service_type']."'
	          or provider_name='".$request['service_type']."' ";
	 $query0 = DB::select($sql0);
	 $service_type_id = $query0[0]->id;
	 $sql="select * from provider_services where provider_id ='".$request['provider_id']."'
		   AND service_type_id ='".$service_type_id."'";
	 $query = DB::select($sql);
		if(count($query) > 0)
		{
		  return response()->json(['status'=>"1",'data'=>$query]);
		}
	    else
	    {				

		  return response()->json(['status'=>"0",'data'=>$query ]);			
		}
	}
	
	
	
	public function get_ser_status(Request $request)
	{ 
	  $sql   ="select id,status,started_at,finished_at from user_requests 
	           where id ='".$request['req_id']."'";
	  $query = DB::select($sql);
	  //echo "<pre>";print_r($query);die;
		if(count($query) > 0)
		{
	     $one   = $query[0]->started_at;
		 $two   = $query[0]->finished_at;
		 $start = date_create($one);
         $end   = date_create($two);
         $diff  = date_diff($end,$start); 
         $acutal= $diff->h.":".$diff->i.":".$diff->s;
        //$query[0]->$acutal;		 
		 return response()->json(['status'=>"1",'timing' => $acutal]);
		}
	    else
	    {				
		 return response()->json(['status'=>"0",'data'=>$query ]);			
		}
	}
	
	
	
	
	
	
	
	 public function ser_bef_image(Request $request)
	{
	     $request_id  = $request['request_id'];
    	 if($request->hasfile('bef_image'))
         {
		   $image = $request->file('bef_image'); 
		   $catagory_icon=$image->getClientOriginalName();
		   $image->move(public_path().'/storage/service/', $catagory_icon);
		   if($request['tag'] == 'before')
		   {	   
		    DB::table('user_requests')
		        ->where('id', $request_id)
		        ->update(
		    array('img1'=> "service/".$catagory_icon,'before_image'=> "service/".$catagory_icon));
		   }else
		   {
			DB::table('user_requests')
		        ->where('id', $request_id)
		        ->update(
		    array('img2'=> "service/".$catagory_icon,'after_image'=> "service/".$catagory_icon));   
		   }
			return response()->json(['status'=>"1",'msg'=>'successfully uploaded' ]);  
          }
		  else
		  {
			return response()->json(['status'=>"0",'msg'=>'please upload image' ]);	
		  }	  
	}
	
	public function update_fcm_token(Request $request)
	{
	  $device_token= $request['device_token'];
      $user_id     = $request['user_id'];
      if(!empty($device_token) && !empty($user_id))
	  {	  		  
		  DB::table('users')
		 ->where('id', $user_id)
		 ->update(
		 array('device_token'=>$device_token));
		 return response()->json(['status'=>"1",'msg'=>'Update successfully' ]);   
      }else
	  {
		return response()->json(['status'=>"0",'msg'=>'Missing fields' ]);  
	  }
	} 
	
	public function send_notifi(Request $request)
	{	
	 $msg1              = $request['msg'];
	 $token = $request['token']; 
	 define( 'API_ACCESS_KEY', 'AAAAKKkZISA:APA91bG_-D3f8weluVLBZTd052iGDu6i-LXPsqM_Ikv8rO2cuS1VnTcfDontEUxdAzC2FNexYIVv4iBMC1H1wRCRywFxdVCZ8TNOtfCdolpYdHEZtjDcG82KWSvB_bU-ZCTxcSqz-BqQ');
	 $msg = array
        (
            'body' => $msg1,
            'title' => 'OUR HIPPY',
              );
    $fields = array
    (
    'to' => $token,
    'notification' => $msg
    );
    $headers = array
    (
    'Authorization: key=' . API_ACCESS_KEY,
    'Content-Type: application/json'
    );
    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    $result = curl_exec($ch );
    echo $result;die;
    curl_close( $ch );  
    echo json_encode(array("status"=>"1","Response"=>"Successfully sent"));
		
	}
    
	
	
    public function update_provider(Request $request)
	{	
	 $id     = $request['provider_id'];
	 $des    = $request['des'];
	 $exp    = $request['exp'];
	 $w_start= $request['w_start'];
	 $out_arr= $request->all();
	 $update = DB::table('providers')
			   ->where('id', $id)
			   ->update(
			   array('description'=>$des,'exp'=>$exp,'w_start'=>$w_start));
	 if($update == true)
	 {
		return response()->json(['status'=>"1",'msg'=>'Successfully Updated','data'=>$out_arr ]); 
	 }else
     {
		return response()->json(['status'=>"0",'msg'=>'Something went wrong','data'=>$out_arr ]); 
	 }		 
    }

	 public function all_request(Request $request)
	{ 
	   print_r(Auth::user()->id);die;
	   echo "<pre>";print_r($request->all());die;
 	   echo '1245';die;
	   $id     = $request['provider_id'];
	   return view('provider.payment.upcoming');
	  
	}   
}	