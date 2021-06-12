<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Socialite;
use DB;
use App\User;
use App\Provider;
use Session;
use Illuminate\Support\Facades\Auth;
use Hesto\MultiAuth\Traits\LogsoutGuard;
use Config;

//use App\Services\SocialGoogleAccountService;
class SocialAuthGoogleController extends Controller
{
  /**
   * Create a redirect method to google api.
   *
   * @return void
   */
   protected $redirectTo = '/provider';

 

    public function redirect()
    {
	Session::forget('type');
	$type = isset($_GET['type']) ? $_GET['type'] : 1;	
	session(['type'=> $type]);
		
        return Socialite::driver('google')->redirect();
    }
/**
     * Return a callback method from google api.
     *
     * @return callback URL from google
     */
  /*  public function callback(SocialGoogleAccountService $service)
    {
        $user = $service->createOrGetUser(Socialite::driver('google')->user());
        auth()->login($user);
        return redirect()->to('/home');
    } */

    
	public function callback(Request $request)
	{		
	    $user1 = Socialite::driver('google')->stateless()->user();
            $type = Session::get('type');
     	
	  if($type == 1){
	       $user = User::where('email', $user1->email)->first();

            if (!$user) {
		$user = new User;
		$user->email = $user1->email;
		$user->password = md5(rand(1,10000));
		$user->first_name = $user1->name;
		$user->save();    
          
            }
     

	    auth()->login($user);
            return redirect()->to('/dashboard');

          }else{

	    $user = Provider::where('email', $user1->email)->first();

        if (!$user) {
		$user = new Provider;
		$user->email = $user1->email;
		$user->password = md5(rand(1,10000));
		$user->first_name = $user1->name;
		$user->save();                
            }
	   
             auth()->login($user);
            return redirect()->to('/dashboard');
		return redirect()->to('/provider');
	  
          }
            
				 
	}


}