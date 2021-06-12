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


class SigninController extends BaseController
{  
    public $var_one ="hello baby";
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	public function sig2(Request $request)
	{
       $con = mysqli_connect("localhost","swxzcxwujy","swxzcxwujy","swxzcxwujy");
	   
	}
		public function SubscribProcess()
		{
			return view('payumoney');
		}
         
		 
		public function Response(Request $request)
		{
			dd('Payment Successfully done!');
		}
		
		
		public function SubscribeCancel()
		{
			 dd('Payment Cancel!');
		}


}