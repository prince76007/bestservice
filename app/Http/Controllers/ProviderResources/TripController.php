<?php

namespace App\Http\Controllers\ProviderResources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\SendPushNotification;
use Auth;
use Setting;
use Storage;
use Carbon\Carbon;

use App\User;
use App\Helpers\Helper;
use App\RequestFilter;
use App\UserRequests;
use App\ProviderService;
use App\PromocodeUsage;
use App\Provider;
use App\Promocode;
use App\UserRequestRating;
use App\UserRequestPayment;
use DB;
class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
     //($request);
        try{

            if($request->ajax()) {
                $Provider = Auth::user();
            } else {
                $Provider = Auth::guard('provider')->user();
            }

            $provider = $Provider->id; 
            
            if($provider=='' || $provider==0){
                 return response()->json(['error' => 'Something went wrong.']);
            }
          //  DB::connection()->enableQueryLog();
           
            $AfterAssignProvider = RequestFilter::with(['request.user',
			'request.payment', 'request' ,'request.service_type'])
                ->where('provider_id', $provider)
                ->whereHas('request', function($query) use ($provider) {
                        $query->where('status','<>', 'CANCELLED');
                        $query->where('status','<>', 'SCHEDULED');
						
						$query->where('status','<>', 'ACCEPTED');
						$query->where('status','<>', 'STARTED');
						$query->where('status','<>', 'ARRIVED');
						$query->where('status','<>', 'PICKEDUP');
						$query->where('status','<>', 'DROPPED');
						
                        $query->where('provider_id', $provider );
                        $query->where('current_provider_id', $provider);
                    });

            $BeforeAssignProvider = RequestFilter::with(['request.user', 'request.payment', 
			 'request','request.service_type'])
                ->where('provider_id', $provider)
                ->whereHas('request', function($query) use ($provider){
                        $query->where('status','<>', 'CANCELLED');
                        $query->where('status','<>', 'SCHEDULED');
						
						$query->where('status','<>', 'ACCEPTED');
					    $query->where('status','<>', 'STARTED');
						$query->where('status','<>', 'ARRIVED');
						$query->where('status','<>', 'PICKEDUP');
						$query->where('status','<>', 'DROPPED');
						
						
                        $query->where('current_provider_id',$provider);
                    });

            $IncomingRequests = $BeforeAssignProvider->union($AfterAssignProvider)->get();
        //    $data=DB::getQueryLog();
           // dd($data);

           /*  if(!empty($request->latitude)) {
                $Provider->update([
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                ]);
            }
 */
            $Timeout = Setting::get('provider_select_timeout', 1800);
            //dd($IncomingRequests);
            
                if(!empty($IncomingRequests)){
                    for ($i=0; $i < sizeof($IncomingRequests); $i++) 
					{
                        $IncomingRequests[$i]->time_left_to_respond = $Timeout - (time() - strtotime($IncomingRequests[$i]->request->assigned_at));
                        if($IncomingRequests[$i]->request->status == 'SEARCHING' && $IncomingRequests[$i]->time_left_to_respond < 0) {
                            $this->assign_next_provider($IncomingRequests[$i]->request->id);
                        }
                    }
                }

            $Response = [
                    'account_status' => $Provider->status,
                    'service_status' => $Provider->service ? Auth::user()->service->status : 'offline',
                    'requests' => $IncomingRequests,
                ];

            return $Response;
            
        

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Something went wrong']);
        }
    }

    /**
     * Cancel given request.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request)
    {
	//echo "<pre>";print_r($request->all());die;
        try{
$req_id = $request->id;
$sql="select u.mobile,u.device_token from user_requests ru join users u on ru.user_id = u.id where ru.id ='$req_id'";
$query = DB::select($sql);
$mobile = $query[0]->mobile;
$message= urlencode('BEST SERVICE: Dear user your request has been accepted by service provider');
/*
$ch     =curl_init('http://182.18.143.11/api/mt/SendSMS?user=balvinders&password=balvinders&senderid=KSBMIT&channel=Trans&DCS=0&flashsms=0&number='.$mobile.'&text='.$message.'&route=1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data   = curl_exec($ch);
curl_close($ch);
*/

# CODE TO SEND NOTIFICATION TO User
	 $msg1              = 'Your request has been Cancel by provider';
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
# END CODE TO SEND NOTIFICATION			
		
			
			

            $UserRequest = UserRequests::findOrFail($request->id);
            $Cancellable = ['SEARCHING', 'ACCEPTED', 'ARRIVED', 'STARTED', 'CREATED','SCHEDULED'];

            if(!in_array($UserRequest->status, $Cancellable)) {
                return back()->with(['flash_error' => 'Cannot cancel request at this stage!']);
            }

            $UserRequest->status = "CANCELLED";
            $UserRequest->cancelled_by = "PROVIDER";
            $UserRequest->save();

             RequestFilter::where('request_id', $UserRequest->id)->delete();

             ProviderService::where('provider_id',$UserRequest->provider_id)->update(['status' =>'active']);

             // Send Push Notification to User
            (new SendPushNotification)->ProviderCancellRide($UserRequest);

            return $UserRequest;

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Something went wrong']);
        }


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function rate(Request $request, $id)
    {

        $this->validate($request, [
                'rating' => 'required|integer|in:1,2,3,4,5',
                'comment' => 'max:255',
            ]);
    
        try {

            $UserRequest = UserRequests::where('id', $id)
                ->where('status', 'COMPLETED')
                ->firstOrFail();

            if($UserRequest->rating == null) {
                UserRequestRating::create([
                        'provider_id' => $UserRequest->provider_id,
                        'user_id' => $UserRequest->user_id,
                        'request_id' => $UserRequest->id,
                        'provider_rating' => $request->rating,
                        'provider_comment' => $request->comment,
                    ]);
            } else {
                $UserRequest->rating->update([
                        'provider_rating' => $request->rating,
                        'provider_comment' => $request->comment,
                    ]);
            }

            $UserRequest->update(['provider_rated' => 1]);

            // Delete from filter so that it doesn't show up in status checks.
            RequestFilter::where('request_id', $id)->delete();

            // Send Push Notification to Provider 
            $base = UserRequestRating::where('user_id', $UserRequest->user_id);
            $average = $base->avg('user_rating');
            $average_count = $base->count();

            $UserRequest->user->update(['rating' => $average,'user_rating' => $average_count ]);

            ProviderService::where('provider_id',$UserRequest->provider_id)->update(['status' =>'active']);

            return response()->json(['message' => 'Request Completed!']);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Request not yet completed!'], 500);
        }
    }

    /**
     * Get the trip history of the provider
     *
     * @return \Illuminate\Http\Response
     */
    public function history(Request $request)
    {
       
        if($request->ajax()) {
//echo Auth::user()->id; die;
          //  DB::connection()->enableQueryLog();

            $Jobs = UserRequests::where('provider_id', Auth::user()->id)->where('status','!=','SCHEDULED')->orderBy('created_at','desc')->with('user', 'service_type', 'payment', 'rating')->get();
       //  $queries = DB::getQueryLog();
         //   $last_query = end($queries);
         //   dd($last_query);

            if(!empty($Jobs)){
                $map_icon = asset('asset/marker.png');
                foreach ($Jobs as $key => $value) {
                    $Jobs[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=320x130&maptype=terrian&format=png&visual_refresh=true&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude."&key=".env('GOOGLE_API_KEY');
                }
            }
            return $Jobs;
        }
        //dd('nooo');
        $Jobs = UserRequests::where('provider_id', Auth::guard('provider')->user()->id)->with('user', 'service_type', 'payment', 'rating')->get();
        return view('provider.trip.index', compact('Jobs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request, $id)
    {
		 //echo "<pre>";print_r($request->all());die;
        try { 
/*
	# This code is used for started at  accept
	date_default_timezone_set('Asia/Kolkata');
	$today   = date('Y-m-d');
	$time  = date('Y-m-d H:i:s');
	$pro_id = Auth::user()->id;
	$sql="update  user_requests set status = 'STARTED',provider_id ='".$pro_id."',
		  current_provider_id ='".$pro_id."',started_at = '$time' where id ='".$id."'";
	$query = DB::select($sql);			
	# code started at accept  
*/
		
$req_id = $id;
$sql="select u.mobile,u.device_token from user_requests ru join users u on ru.user_id = u.id where ru.id ='$req_id'";

$query = DB::select($sql);
$mobile = $query[0]->mobile;
$message= urlencode('BEST SERVICE: Dear user your request has been accepted by service provider');
/*
$ch     =curl_init('http://182.18.143.11/api/mt/SendSMS?user=balvinders&password=balvinders&senderid=KSBMIT&channel=Trans&DCS=0&flashsms=0&number='.$mobile.'&text='.$message.'&route=1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data   = curl_exec($ch);
curl_close($ch);
*/

# CODE TO SEND NOTIFICATION TO User
	 $msg1              = 'Your request has been accepted by provider';
	 $token = $query[0]->device_token; 
            $API_ACCESS_KEY='AAAAKKkZISA:APA91bG_-D3f8weluVLBZTd052iGDu6i-LXPsqM_Ikv8rO2cuS1VnTcfDontEUxdAzC2FNexYIVv4iBMC1H1wRCRywFxdVCZ8TNOtfCdolpYdHEZtjDcG82KWSvB_bU-ZCTxcSqz-BqQ';
    /*
    if(API_ACCESS_KEY==''){
	 define( 'API_ACCESS_KEY', 'AAAAKKkZISA:APA91bG_-D3f8weluVLBZTd052iGDu6i-LXPsqM_Ikv8rO2cuS1VnTcfDontEUxdAzC2FNexYIVv4iBMC1H1wRCRywFxdVCZ8TNOtfCdolpYdHEZtjDcG82KWSvB_bU-ZCTxcSqz-BqQ');
    }
    */
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
    'Authorization: key=' . $API_ACCESS_KEY,
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
  
# END CODE TO SEND NOTIFICATION	


            $UserRequest = UserRequests::findOrFail($id);

           if($UserRequest->status != "SEARCHING") {
                return response()->json(['error' => 'Request already under progress!']);
            }
            
            $UserRequest->provider_id = Auth::user()->id;
/*
            if($UserRequest->schedule_at != ""){

                $beforeschedule_time = strtotime($UserRequest->schedule_at."- 1 hour");
                $afterschedule_time = strtotime($UserRequest->schedule_at."+ 1 hour");

                $CheckScheduling = UserRequests::where('status','SCHEDULED')
                            ->where('provider_id', Auth::user()->id)
                            ->whereBetween('schedule_at',[$beforeschedule_time,$afterschedule_time])
                            ->count();

                if($CheckScheduling > 0 ){
                    if($request->ajax()) {
                        return response()->json(['error' => trans('api.ride.request_already_scheduled')]);
                    }else{
                        return redirect('dashboard')
                                ->with('flash_error', 'If the ride is already scheduled then we cannot schedule/request another ride for the after 1 hour or before 1 hour');
                    }
                }


                RequestFilter::where('request_id',$UserRequest->id)->where('provider_id',Auth::user()->id)->update(['status' => 2]);

                $UserRequest->status = "SCHEDULED";
                $UserRequest->save();

                // Send Push Notification to User
                (new SendPushNotification)->RideScheduled($UserRequest);

            }else{

*/
                $UserRequest->status = "SCHEDULED";
                $UserRequest->save();

                  // $this->update($request,$req_id);
                ProviderService::where('provider_id',$UserRequest->provider_id)->update(['status' =>'riding']);

                $Filters = RequestFilter::where('request_id', $UserRequest->id)->where('provider_id', '!=', Auth::user()->id)->get();
                //dd($Filters->toArray());
                foreach ($Filters as $Filter) {
                    $Filter->delete();
                }
          //  }

            $UnwantedRequest = RequestFilter::where('request_id','!=' ,$UserRequest->id)
                                ->where('provider_id',Auth::user()->id )
                                ->whereHas('request', function($query){
                                    $query->where('status','<>','SCHEDULED');
                                });

            if($UnwantedRequest->count() > 0){
                $UnwantedRequest->delete();
            } 

            // Send Push Notification to User
            (new SendPushNotification)->RideAccepted($UserRequest);
          //  $data_request=$this->update($request, $id);
            //dd($data_request);
          //  $data_request->before_image='http://www.bestservicepoint.com/storage/app/public/'.$data_request->before_image;
          //  $data_request->after_image='http://www.bestservicepoint.com/storage/app/public/'.$data_request->after_image;
            return $UserRequest;

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Unable to accept, Please try again later']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Connection Error']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
     //  dd($request);
        $this->validate($request, [
              'status' => 'required|in:ACCEPTED,STARTED,ARRIVED,PICKEDUP,DROPPED,PAYMENT,COMPLETED',
              'before_image' => 'mimes:jpeg,jpg,bmp,png',
              'after_image' => 'mimes:jpeg,jpg,bmp,png',
              'after_comment' => 'max:255',
              'before_comment' => 'max:255',
           ]);

        try{
			
$req_id = $id;
$sql="select u.mobile,u.device_token,u.id as 'user_id' from user_requests ru join users u on ru.user_id = u.id where ru.id ='$req_id'";
$query = DB::select($sql);
$user_id=$query[0]->user_id;
$mobile = $query[0]->mobile;
$message= urlencode('BEST SERVICE: Dear user your request has been accepted by service provider');
# CODE TO SEND NOTIFICATION TO User
if($request->status =='DROPPED')
{
	 $msg1              = 'Your request has been END by provider';
}else if($request->status =='COMPLETED')
{
	$msg1              = 'Your request has been COMPLETED by provider';
}
else if($request->status == 'STARTED')
{
  $msg1    = 'Your request has been STARTED by provider';
}
else
{
	$msg1              = 'Please check your Best Service App';
}
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
			

            $UserRequest = UserRequests::with('user')->findOrFail($id);

            if($request->has('before_comment')){
                $UserRequest->before_comment = $request->before_comment;
            }

            if($request->has('after_comment')){
                $UserRequest->after_comment = $request->after_comment;
            }

            if ($request->hasFile('before_image')) {
                $UserRequest->before_image = $request->before_image->store('service');
            }

            if ($request->hasFile('after_image'))
		    {
                $UserRequest->after_image = $request->after_image->store('service');
            }

            if($request->status == 'DROPPED' && $UserRequest->payment_mode != 'CASH')
		    {
                $UserRequest->status = 'COMPLETED';
            } else if ($request->status == 'COMPLETED' && $UserRequest->payment_mode == 'CASH') 
			{
                $UserRequest->status = $request->status;
                $UserRequest->paid = 1;
                ProviderService::where('provider_id',$UserRequest->provider_id)->update(['status' =>'active']);
                ProviderService::where('provider_id',$UserRequest->provider_id)->where('status','riding')->update(['status' =>'active']);
            } else 
			{
                $UserRequest->status = $request->status;
               
                if($request->status == 'ARRIVED')
				{
                    (new SendPushNotification)->Arrived($UserRequest);
                }
                if($request->status == 'PICKEDUP')
				{
                    $UserRequest->started_at = Carbon::now();
                    $UserRequest->save();
                }
                
            }
  /*			
   if($request->status == 'STARTED')
   {
	date_default_timezone_set('Asia/Kolkata');
    $today   = date('Y-m-d');
    $time  = date('Y-m-d H:i:s');
	    
	DB::table('user_requests')
   ->where('id', $id)
   ->update(
   array('started_at'=> $time));  
   }
   */
   
            $UserRequest->save();

            if($request->status == 'DROPPED') {
                
                (new SendPushNotification)->user_dropped($UserRequest);
                $UserRequest->with('user')->findOrFail($id);
                $UserRequest->finished_at = Carbon::now();
                $UserRequest->save();
                $UserRequest->invoice = $this->invoice($id);
                return $UserRequest;
            }

            // Send Push Notification to User
       //dd($UserRequest); 

       if($request->status=='COMPLETED'){
        // $data=DB::table('referral_data')->where('user_id',$user_id)->first();
        // if($data!=null && $data!=''){
        //     if(($data['referral_by']!=null && $data['referral_by']!='') && $data['status']=='active'){
        //        // $ref_by=DB::table('users')->where('referral_id',$data['referral_by'])->first();
        //         //   $getUserRequest=UserRequest::where('user_id',$user_id);
        //         $getUserRequest=DB::table('user_requests')->where('user_id',$user_id)->get();
        //         if($getUserRequest->count()==1){
        //             $userReqId=$getUserRequest['id'];
        //             $userReqPayment=DB::table('user_request_payments')->where('request_id',$userReqId)->first();
        //             $amount=(int)$userReqPayment['total'];
        //              $ref_by=User::findOrFail($data['referral_by']);
        //              $ref_by->wallet_balance=($amount/100) * 5;
        //              $ref_by->save();
        //         }
                
        //         $refdata=array("status"=>"deactive");
        //         DB::table('referral_data')->where('user_id',$user_id)->update($refdata);
        //     }elseif(($data['referral_by']==null || $data['referral_by']=='')&& $data['status']=='active'){
        //         $refdata=array("status"=>"deactive");
        //         DB::table('referral_data')->where('user_id',$user_id)->update($refdata);
        //     }
        // }
    
           
           
       }
            return $UserRequest;

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Unable to update, Please try again later']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Connection Error']);
        }
    }

    
    
     public function update_image(Request $request, $id)
    {
     //  dd($request);
        $this->validate($request, [
              'before_image' => 'mimes:jpeg,jpg,bmp,png',
              'after_image' => 'mimes:jpeg,jpg,bmp,png',
              'after_comment' => 'max:255',
              'before_comment' => 'max:255',
           ]);

        try{
			
$req_id = $id;
	
            $UserRequest = UserRequests::with('user')->findOrFail($id);

            if($request->has('before_comment')){
                $UserRequest->before_comment = $request->before_comment;
            }

            if($request->has('after_comment')){
                $UserRequest->after_comment = $request->after_comment;
            }

            if ($request->type==='0') {
                $UserRequest->before_image = $request->before_image->store('service');
            }

            if ($request->type==='1')
		    {
                $UserRequest->after_image = $request->after_image->store('service');
            }

           
  
   
            $UserRequest->save();

          

            // Send Push Notification to User
       //dd($UserRequest); 
            return $UserRequest;

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Unable to update, Please try again later']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Connection Error']);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $UserRequest = UserRequests::find($id);

        try {
            $this->assign_next_provider($UserRequest->id);
            return $UserRequest->with('user')->get();

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Unable to reject, Please try again later']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Connection Error']);
        }
    }

    public function assign_next_provider($request_id) {

        try {
            $UserRequest = UserRequests::findOrFail($request_id);
        } catch (ModelNotFoundException $e) {
            // Cancelled between update.
            return false;
        }

        $RequestFilter = RequestFilter::where('provider_id', $UserRequest->current_provider_id)
            ->where('request_id', $UserRequest->id)
            ->delete();

        try {

            $next_provider = RequestFilter::where('request_id', $UserRequest->id)
                            ->orderBy('id')
                            ->firstOrFail();

            $UserRequest->current_provider_id = $next_provider->provider_id;
            $UserRequest->assigned_at = Carbon::now();
            $UserRequest->save();

            // incoming request push to provider
            (new SendPushNotification)->IncomingRequest($next_provider->provider_id);
            
        } catch (ModelNotFoundException $e) {
            UserRequests::where('id', $UserRequest->id)->update(['status' => 'CANCELLED']);

            // No longer need request specific rows from RequestMeta
            RequestFilter::where('request_id', $UserRequest->id)->delete();

            //  request push to user provider not available
            (new SendPushNotification)->ProviderNotAvailable($UserRequest->user_id);
        }
    }

    public function invoice($request_id)
    {
        try {
            $UserRequest = UserRequests::findOrFail($request_id);

            $hourdiff = round((strtotime($UserRequest->finished_at) - strtotime($UserRequest->started_at))/3600, 1);
                        
            //$Fixed = $UserRequest->service_type->fixed ? : 0;
            $service_type_id=$UserRequest->service_type_id;
         $ProviderService=   ProviderService::where(['service_type_id'=>$service_type_id,'provider_id'=>$UserRequest->provider_id])->first();
            $Fixed=$ProviderService->service_ki_price;
            $TimePrice = ceil($hourdiff) * $UserRequest->service_type->price;
            $Discount = 0; // Promo Code discounts should be added here.

            if($PromocodeUsage = PromocodeUsage::where('user_id',$UserRequest->user_id)->where('status','ADDED')->first()){
                if($Promocode = Promocode::find($PromocodeUsage->promocode_id)){
                    $Discount = $Promocode->discount;
                    $PromocodeUsage->status ='USED';
                    $PromocodeUsage->save();
                }
            }
            $Wallet = 0;


            $Total = $Fixed + $TimePrice - $Discount;

            $Commision = $Total * (Setting::get('commision_percentage', 10) / 100);
            $Tax = $Total * (Setting::get('tax_percentage', 10) / 100);

            $Total += $Tax;

            if($Total < 0){
                $Total = 0.00; // prevent from negative value
            }
            
            $Payment = new UserRequestPayment;
            $Payment->request_id = $UserRequest->id;
            $Payment->fixed = $Fixed;
            $Payment->time_price = $TimePrice;
            $Payment->commision = $Commision;
            if($Discount != 0 && $PromocodeUsage){
                $Payment->promocode_id = $PromocodeUsage->promocode_id;
            }
            $Payment->discount = $Discount;

            if($UserRequest->use_wallet == 1 && $Total > 0){

                $User = User::find($UserRequest->user_id);

                $Wallet = $User->wallet_balance;

                if($Wallet != 0){

                    if($Total > $Wallet){

                        $Payment->wallet = $Wallet;
                        $Payable = $Total - $Wallet;
                        User::where('id',$UserRequest->user_id)->update(['wallet_balance' => 0 ]);
                        $Payment->total = abs($Payable);

                        // charged wallet money push 
                        (new SendPushNotification)->ChargedWalletMoney($UserRequest->user_id,currency($Wallet));

                    }else{

                        $Payment->total = 0;
                        $WalletBalance = $Wallet - $Total;
                        User::where('id',$UserRequest->user_id)->update(['wallet_balance' => $WalletBalance]);
                        $Payment->wallet = $Total;

                        // charged wallet money push 
                        (new SendPushNotification)->ChargedWalletMoney($UserRequest->user_id,currency($Total));
                    }

                }

            }else{
                $Payment->total = abs($Total);
            }

            $Payment->tax = $Tax;
            $Payment->save();

            return $Payment;

        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    /**
     * Get the trip history details of the provider
     *
     * @return \Illuminate\Http\Response
     */
    public function history_details(Request $request)
    {
        $this->validate($request, [
                'request_id' => 'required|integer|exists:user_requests,id',
            ]);

        if($request->ajax()) {
            
            $Jobs = UserRequests::where('id',$request->request_id)
                                ->where('provider_id', Auth::user()->id)
                                ->orderBy('created_at','desc')
                                ->with('payment','service_type','user','rating')
                                ->get();
            if(!empty($Jobs)){
                $map_icon = asset('asset/marker.png');
                foreach ($Jobs as $key => $value) {
                    $Jobs[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=320x130&maptype=terrian&format=png&visual_refresh=true&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude."&key=".env('GOOGLE_API_KEY');
                }
            }
            foreach($Jobs as $key=>$value){
                $Jobs[$key]['before_image']='http://bestservicepoint.com/storage/app/public/'.$value['before_image'];
                $Jobs[$key]['after_image']='http://bestservicepoint.com/storage/app/public/'.$value['after_image'];
                
            }

            return $Jobs;
        }

    }

        /**
     * Get the trip history details of the provider
     *
     * @return \Illuminate\Http\Response
     */
    public function upcoming_details(Request $request)
    {
        $this->validate($request, [
                'request_id' => 'required|integer|exists:user_requests,id',
            ]);

        if($request->ajax()) {
            
            $Jobs = UserRequests::where('id',$request->request_id)
                                ->where('provider_id', Auth::user()->id)
                                ->with('service_type','user')
                                ->get();
            if(!empty($Jobs)){
                $map_icon = asset('asset/marker.png');
                foreach ($Jobs as $key => $value) {
                    $Jobs[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=320x130&maptype=terrian&format=png&visual_refresh=true&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude."&key=".env('GOOGLE_MAP_KEY');
                }
            }

            return $Jobs;
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function upcoming_trips() {
    
        try{
            $UserRequests = UserRequests::ProviderUpcomingRequest(Auth::user()->id)->get();
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
     * Get the trip history details of the provider
     *
     * @return \Illuminate\Http\Response
     */
    public function summary(Request $request)
    {
        try{
            if($request->ajax()) {

                $rides = UserRequests::where('provider_id', Auth::user()->id)->count();
                $revenue = UserRequestPayment::whereHas('request', function($query){
                                $query->where('provider_id', Auth::user()->id);
                            })
                        ->sum('total');
                $cancel_rides = UserRequests::where('status','CANCELLED')->where('provider_id', Auth::user()->id)->count();
                $scheduled_rides = UserRequests::where('status','SCHEDULED')->where('provider_id', Auth::user()->id)->count();

                return response()->json([
                    'rides' => $rides, 
                    'revenue' => $revenue,
                    'cancel_rides' => $cancel_rides,
                    'scheduled_rides' => $scheduled_rides,
                ]);
            }

        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }

    }


    /**
     * help Details.
     *
     * @return \Illuminate\Http\Response
     */

    public function help_details(Request $request){

        try{

            if($request->ajax()) {
                return response()->json([
                        'contact_number' => Setting::get('contact_number',''), 
                        'contact_email' => Setting::get('contact_email',''),
                        'contact_text' => Setting::get('contact_text',''),
                        'contact_title' => Setting::get('site_title',''),
                     ]);
            }

        }catch (Exception $e) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')]);
            }
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
            
             $msg1              = 'Your request has been accepted by provider';
	 $token = $query[0]->device_token; 
            $API_ACCESS_KEY='AAAAKKkZISA:APA91bG_-D3f8weluVLBZTd052iGDu6i-LXPsqM_Ikv8rO2cuS1VnTcfDontEUxdAzC2FNexYIVv4iBMC1H1wRCRywFxdVCZ8TNOtfCdolpYdHEZtjDcG82KWSvB_bU-ZCTxcSqz-BqQ';
    /*
    if(API_ACCESS_KEY==''){
	 define( 'API_ACCESS_KEY', 'AAAAKKkZISA:APA91bG_-D3f8weluVLBZTd052iGDu6i-LXPsqM_Ikv8rO2cuS1VnTcfDontEUxdAzC2FNexYIVv4iBMC1H1wRCRywFxdVCZ8TNOtfCdolpYdHEZtjDcG82KWSvB_bU-ZCTxcSqz-BqQ');
    }
    */
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
    'Authorization: key=' . $API_ACCESS_KEY,
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
            
            
            
            
            
            

 $msg1              = 'Your request has been accepted by provider';
	 $token = $query[0]->device_token; 
            $API_ACCESS_KEY='AAAAKKkZISA:APA91bG_-D3f8weluVLBZTd052iGDu6i-LXPsqM_Ikv8rO2cuS1VnTcfDontEUxdAzC2FNexYIVv4iBMC1H1wRCRywFxdVCZ8TNOtfCdolpYdHEZtjDcG82KWSvB_bU-ZCTxcSqz-BqQ';
    /*
    if(API_ACCESS_KEY==''){
	 define( 'API_ACCESS_KEY', 'AAAAKKkZISA:APA91bG_-D3f8weluVLBZTd052iGDu6i-LXPsqM_Ikv8rO2cuS1VnTcfDontEUxdAzC2FNexYIVv4iBMC1H1wRCRywFxdVCZ8TNOtfCdolpYdHEZtjDcG82KWSvB_bU-ZCTxcSqz-BqQ');
    }
    */
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
    'Authorization: key=' . $API_ACCESS_KEY,
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

}
