<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserRequestPayment;
use App\UserRequests;
use App\Card;
use App\User;
use App\Http\Controllers\SendPushNotification;
use DB;
use Setting;
use Exception;
use Auth;
use Illuminate\Contracts\Auth\Authenticatable;


class PaymentController extends Controller
{
	/**
     * payment for user.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment(Request $request){

    	$this->validate($request, [
    			'request_id' => 'required|exists:user_request_payments,request_id|exists:user_requests,id,paid,0,user_id,'.Auth::user()->id
    		]);


    	$UserRequest = UserRequests::find($request->request_id);

    	if($UserRequest->payment_mode == 'CARD'){

    		$RequestPayment = UserRequestPayment::where('request_id',$request->request_id)->first(); 

    		$StripeCharge = $RequestPayment->total * 100;

            if($RequestPayment->total > 0){


          		try{

          			$Card = Card::where('user_id',Auth::user()->id)->where('is_default',1)->first();

      	    		\Stripe\Stripe::setApiKey(Setting::get('stripe_secret_key'));

      	    		$Charge = \Stripe\Charge::create(array(
      					  "amount" => $StripeCharge,
      					  "currency" => "usd",
      					  "customer" => Auth::user()->stripe_cust_id,
      					  "card" => $Card->card_id,
      					  "description" => "Payment Charge for ".Auth::user()->email,
      					  "receipt_email" => Auth::user()->email
      					));

      	    		$RequestPayment->payment_id = $Charge["id"];
      	    		$RequestPayment->payment_mode = 'CARD';
      	    		$RequestPayment->save();

      	    		$UserRequest->paid = 1;
      	    		$UserRequest->status = 'COMPLETED';
      	    		$UserRequest->save();

                    if($request->ajax()){
                  	   return response()->json(['message' => trans('api.paid')]); 
                    }else{
                        return redirect('dashboard')->with('flash_success','Paid');
                    }

          		} catch(\Stripe\StripeInvalidRequestError $e){
                    if($request->ajax()){
          			     return response()->json(['error' => $e->getMessage()], 500);
                    }else{
                          return back()->with('flash_error',$e->getMessage());
                    }
          		} 

            }if($RequestPayment->total == 0){

                $RequestPayment->payment_mode = 'CARD';
                $RequestPayment->save();

                $UserRequest->paid = 1;
                $UserRequest->status = 'COMPLETED';
                $UserRequest->save();


                if($request->ajax()){
                   return response()->json(['message' => trans('api.paid')]); 
                }else{
                    return redirect('dashboard')->with('flash_success','Paid');
                }

            }else{
                return back()->with('flash_error','Try again later');
            }

    	}else{
                return back()->with('flash_error','Try again later');
        }
    }


    /**
     * add wallet money for user.
     *
     * @return \Illuminate\Http\Response
     */


public function paymentResponse1(Request $request)
{
  
$status=$request->status ;
$firstname=$request->firstname;
$amount=$request->amount;
$txnid=$request->txnid;
$posted_hash=$request->hash;
$key=$request->key;
$productinfo=$request->productinfo;
$email=$request->email;
$salt="";
//dd($request);
// Salt should be same Post Request 

If (isset($request->additionalCharges)) {
       $additionalCharges=$request->additionalCharges;
        $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
  } else {
        $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
         }
     $hash = hash("sha512", $retHashSeq);
       if ($status!='success') {
         echo "Invalid Transaction. Please try again";
       } else {

          $user_id =$request['user_id'];
           $sql2 = "UPDATE users set wallet_balance = '".$amount."', verify_flag='1' where id = '$request->user_id' ";
           $query= DB::select($sql2);
            $user = User::where('id' , $user_id)->first();
         return redirect('wallet')->with('flash_success',' added to your wallet');

         /* $msg= "Thank You. Your trnasaction has been ". $status .".";
          $msg= $msg." Transaction ID for this transaction is ".$txnid;
          $msg=$msg. " We have received a payment of Rs. " . $amount . " ".$productinfo;
             return redirect('dashboard')->with('flash_alert',$msg);*/
       }
}
    
public function paymentCancel1(Request $request)
{
   $status=$_POST["status"];
$firstname=$_POST["firstname"];
$amount=$_POST["amount"];
$txnid=$_POST["txnid"];

$posted_hash=$_POST["hash"];
$key=$_POST["key"];
$productinfo=$_POST["productinfo"];
$email=$_POST["email"];
$salt="";

// Salt should be same Post Request 

If (isset($_POST["additionalCharges"])) {
       $additionalCharges=$_POST["additionalCharges"];
        $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
  } else {
        $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
         }
     $hash = hash("sha512", $retHashSeq);
  
       if ($hash != $posted_hash) {
       
             return redirect('wallet')->with('flash_error','Invalid Transaction. Please try again');
       } else {
         echo "<h3>Your order status is ". $status .".</h3>";
         echo "<h4>Your transaction id for this transaction is ".$txnid.". You may try making the payment by clicking the link below.</h4>";
     } 
    
}



function saveMoney(Request $request)
{
  
  $user_id =$request['user_id'];
  $amt = $request['amount'];
     $sql2 = "UPDATE users set wallet_balance = '".$amt."', verify_flag='1' where id = '$request->user_id' ";
     $query          = DB::select($sql2);
     $user           = User::where('id' , $user_id)->first();
     //return response()->json(['status'=>"1",'data'=>$user]);
     return redirect('wallet')->with('flash_success',' added to your wallet');
}


public function add_money(Request $request)
{


	$id=$request['user_id'];

 
	 $sql="select * from users where id ='".$request['user_id']."'";
	 $query = DB::select($sql);
     if(count($query) > 0)
	 {	
		 $wallet_balance = 	$query[0]->wallet_balance;
		 $sql2           = "UPDATE users set wallet_balance = '$wallet_balance'+'".$request['amount']."' where id = '$request->user_id' ";
		 $query          = DB::select($sql2);
		 $user           = User::where('id' , $request->user_id)->first();
		 return response()->json(['status'=>"1",'data'=>$user]);
    }
    else 
    {				
       return response()->json(['status'=>"0" ]);			

    }

// here is end of code

        $this->validate($request, [
                'amount' => 'required|integer',
                'card_id' => 'required|exists:cards,card_id,user_id,'.Auth::user()->id
            ]);

        try{
            
            $StripeWalletCharge = $request->amount * 100;

            \Stripe\Stripe::setApiKey(Setting::get('stripe_secret_key'));

            $Charge = \Stripe\Charge::create(array(
                  "amount" => $StripeWalletCharge,
                  "currency" => "usd",
                  "customer" => Auth::user()->stripe_cust_id,
                  "card" => $request->card_id,
                  "description" => "Adding Money for ".Auth::user()->email,
                  "receipt_email" => Auth::user()->email
                ));

            $update_user = User::find(Auth::user()->id);
            $update_user->wallet_balance += $request->amount;
            $update_user->save();

            Card::where('user_id',Auth::user()->id)->update(['is_default' => 0]);
            Card::where('card_id',$request->card_id)->update(['is_default' => 1]);

            //sending push on adding wallet money
            (new SendPushNotification)->WalletMoney(Auth::user()->id,currency($request->amount));

            if($request->ajax()){
               return response()->json(['message' => currency($request->amount).trans('api.added_to_your_wallet'), 'user' => $update_user]); 
            }else{
                return redirect('wallet')->with('flash_success',currency($request->amount).' added to your wallet');
            }

        } catch(\Stripe\StripeInvalidRequestError $e){
            if($request->ajax()){
                 return response()->json(['error' => $e->getMessage()], 500);
            }else{
                return back()->with('flash_error',$e->getMessage());
            }
        } 

    }
    
    
public function paymentProcess()
{
    return view('payumoney');
}
    
public function paymentResponse(Request $request)
{
  
$status=$request->status ;
$firstname=$request->firstname;
$amount=$request->amount;
$txnid=$request->txnid;
$posted_hash=$request->hash;
$key=$request->key;
$productinfo=$request->productinfo;
$email=$request->email;
$salt="";
//dd($request);
// Salt should be same Post Request 

If (isset($request->additionalCharges)) {
       $additionalCharges=$request->additionalCharges;
        $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
  } else {
        $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
         }
		 $hash = hash("sha512", $retHashSeq);
       if ($status!='success') {
	       echo "Invalid Transaction. Please try again";
		   } else {
          $msg= "Thank You. Your trnasaction has been ". $status .".";
          $msg= $msg." Transaction ID for this transaction is ".$txnid;
          $msg=$msg. " We have received a payment of Rs. " . $amount . " ".$productinfo;
             return redirect('dashboard')->with('flash_alert',$msg);
		   }
}
    
public function paymentCancel(Request $request)
{
   $status=$_POST["status"];
$firstname=$_POST["firstname"];
$amount=$_POST["amount"];
$txnid=$_POST["txnid"];

$posted_hash=$_POST["hash"];
$key=$_POST["key"];
$productinfo=$_POST["productinfo"];
$email=$_POST["email"];
$salt="";

// Salt should be same Post Request 

If (isset($_POST["additionalCharges"])) {
       $additionalCharges=$_POST["additionalCharges"];
        $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
  } else {
        $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
         }
		 $hash = hash("sha512", $retHashSeq);
  
       if ($hash != $posted_hash) {
	     
             return redirect('dashboard')->with('flash_error','Invalid Transaction. Please try again');
		   } else {
         echo "<h3>Your order status is ". $status .".</h3>";
         echo "<h4>Your transaction id for this transaction is ".$txnid.". You may try making the payment by clicking the link below.</h4>";
		 } 
    
}        
    


public function LoadwalletpaymentResponse(Request $request)
{
     
    $status=$_POST["status"];  
    $firstname = $_POST['firstname'];
        $amount = $_POST['amount'];
        $txnid = $_POST['txnid'];
        $posted_hash = $_POST['hash'];
        $key = $_POST['key'];
        $productinfo = $_POST['productinfo'];
        $email = $_POST['email'];
        $salt = "uxptDjtOjI"; //  Your salt
        if (isset($request->additionalCharges)) {
            $additionalCharges = $_POST['additionalCharges'];
             $retHashSeq = $additionalCharges . '|' . $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        } else {

            $retHashSeq = $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        }

        $hash = hash("sha512", $retHashSeq);
  
  if (isset($request->additionalCharges)) {
       $additionalCharges=$request->additionalCharges;
    
      $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
   
        $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
  } else {
        $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
         }
     $hash = hash("sha512", $retHashSeq);
       if ($status!='success') {
        return redirect('wallet')->with('flash_error','Invalid Transaction. Please try again');
        // echo "Invalid Transaction. Please try again";
      } else {
          
          $msg= "Thank You. Your trnasaction has been ". $status .".";
          $msg.=" Transaction ID for this transaction is ".$txnid;
          $msg.= " We have received a payment of Rs. " . $amount . " ".$productinfo;
      
       
        if(isset(Auth::user()->id)){

           $update_user = User::find(Auth::user()->id);
            $update_user->wallet_balance += $request->amount;
            $update_user->save();

           // $user_id =Auth::user()->id;
           // $sql2 = "UPDATE users set wallet_balance=wallet_balance+'".$amount."',  where id = '$user_id' ";
           // $query= DB::select($sql2);
           //  $user = User::where('id' , $user_id)->first();

          return redirect('wallet')->with('flash_success',$msg);
        }else{
          $msg="Error user not login";
         return redirect('dashboard')->with('flash_alert',$msg);
        }
       //   return redirect('dashboard')->with('flash_alert',$msg);
       }
}
    
public function LoadwalletpaymentCancel(Request $request)
{

  if(!isset(Auth::user()->id)){
         $msg="Error user not login";
         return redirect('dashboard')->with('flash_alert',$msg);
  }

   $status=$_POST["status"];

//print_r($request);

//dd($request);

// Salt should be same Post Request 


         $firstname = $_POST['firstname'];
        $amount = $_POST['amount'];
        $txnid = $_POST['txnid'];
        $posted_hash = $_POST['hash'];
        $key = $_POST['key'];
        $productinfo = $_POST['productinfo'];
        $email = $_POST['email'];
        $salt = "uxptDjtOjI"; //  Your salt
        if (isset($request->additionalCharges)) {
         $additionalCharges = $_POST['additionalCharges'];
             $retHashSeq = $additionalCharges . '|' . $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        } else {

            $retHashSeq = $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        }

        $hash = hash("sha512", $retHashSeq);
  
       if ($hash != $posted_hash) {
       
             return redirect('wallet')->with('flash_error','Invalid Transaction. Please try again');
       } else {
         $msg= "Your order status is ". $status ."";
         $msg.="Your transaction id for this transaction is ".$txnid.". You may try making the payment by clicking the link below.";
             return redirect('wallet')->with('flash_error',$msg);
    
     } 
    
}        



}
