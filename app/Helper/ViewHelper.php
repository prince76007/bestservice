<?php

use App\PromocodeUsage; 
use App\Provider;
function phone_formate($phone)
{
  if($phone != ""){
   return $maskedPhone = substr($phone, 0, 4) . "****" . substr($phone, 7, 4);
  }else{
   return '-';  
  }
}

function custom_current_add_date_time(){
      date_default_timezone_set('Asia/Kolkata');
      $current_datetime1=date('Y-m-d H:i:s');
      return date('Y-m-d H:i:s', strtotime($current_datetime1.' + 2 minute'));
}

function custom_current_date_time(){
      date_default_timezone_set('Asia/Kolkata');
      return date('Y-m-d H:i:s');
}

 function getProviderId(){
  $prifix='PR';
  $id=substr(str_shuffle("0123456789"), 0, 6);
  $uniq=$prifix.$id;
  $provider=Provider::where('uniq_no',$uniq)->first();
  if(!empty($provider) && $provider!=null){
      $id=substr(str_shuffle("0123456789"), 0, 6);
      $uniq=$prifix.$id;
  }
  return $uniq;
}

function currency($value = '')
{
	if($value == ""){
		return Setting::get('currency')."0";
	}else{
		return Setting::get('currency').$value;
	}
}

function distance($value = '')
{
    if($value == ""){
        return "0".Setting::get('distance', 'Km');
    }else{
        return $value.Setting::get('distance', 'Km');
    }
}

function img($img){
	if($img == ""){
		return asset('main/avatar.jpg');
	}else if (strpos($img, 'http') !== false) {
        return $img;
    }else{
		return asset('storage/'.$img);
	}
}

function promo_used_count($promo_id)
{
	return PromocodeUsage::where('status','USED')->where('promocode_id',$promo_id)->count();
}

function curl($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $return = curl_exec($ch);
    curl_close ($ch);
    return $return;
}



if(!function_exists('send_sms')){
  function send_sms($To,$smsBody) {
  $api_key = '3606AF922E656A';// '26049A5DF6412E';
  $mobile = $To;
  //$from = 'TXTSMS';
  $from = 'PWSSMS';
  $message = urlencode($smsBody);
  $curl = curl_init();
  $url='http://msg.pwasms.com/app/smsapi/index.php?key='.$api_key.'&campaign=0&routeid=68&type=text&contacts='.$mobile.'&senderid=PWASMS&msg='.$message.'';
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

   // EXECUTE:
   $result = curl_exec($curl);
   curl_close($curl);
   return $result;

  }
}


