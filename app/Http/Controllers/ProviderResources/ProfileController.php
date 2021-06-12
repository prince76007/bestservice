<?php

namespace App\Http\Controllers\ProviderResources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use Setting;
use Storage;

use App\ProviderProfile;
use App\ProviderService;

class ProfileController extends Controller
{
    /**
     * Create a new user instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('provider.api', [
            'except' => ['show', 'store', 'available', 'location_edit', 'location_update']
            ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         try {

            Auth::user()->service = ProviderService::where('provider_id',Auth::user()->id)
                                            ->with('service_type')
                                            ->first();
            Auth::user()->currency = Setting::get('currency', '$');

            return Auth::user();

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if(Setting::get('demo_mode', 0) == 1) {
            return back()->with('flash_error', 'Disabled for demo purposes! Please contact us at info@dragon.com');
        }

        $this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'mobile' => 'required',
                'avatar' => 'mimes:jpeg,bmp,png',
                'language' => 'max:255',
                'address' => 'max:255',
                'address_secondary' => 'max:255',
                'city' => 'max:255',
                'country' => 'max:255',
                'postal_code' => 'max:255',
              
            ]);


        try {

            $Provider = Auth::user();

            if($request->has('first_name')) 
                $Provider->first_name = $request->first_name;

            if($request->has('last_name')) 
                $Provider->last_name = $request->last_name;

            if ($request->has('mobile'))
                $Provider->mobile = $request->mobile;

            if ($request->has('description'))
                $Provider->description = $request->description;

            if ($request->has('provider_url'))
                $Provider->provider_url = $request->provider_url;
   

             if ($request->hasFile('avatar')) {
                $Provider->avatar = $request->avatar->store('provider/profile');
            }

            if($Provider->profile) {
                $Provider->profile->update([
                        'address' => $request->address ? : $Provider->profile->address,
                    ]);
            } else {
                ProviderProfile::create([
                        'provider_id' => $Provider->id,
                        'address' => $request->address,
                    ]);
            }


            $Provider->save();

            return redirect(route('provider.profile.index'))->with('flash_success','Profile Updated');
        }

        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Provider Not Found!'], 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $Provider = ProviderService::where('provider_id',Auth::user()->id)
                                            ->with('service_type')
                                            ->get();
        return view('provider.profile.index',compact('Provider'));
    }
    public function allservices()
	{
		echo "ok";die;
	}
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
//dd($request);
     
                if(Setting::get('demo_mode', 0) == 1) {
            return back()->with('flash_error', 'Disabled for demo purposes! Please contact us at info@dragon.com');
        }

        $this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'mobile' => 'required',
                'address' => 'max:255',
            ]);

        try {

            $Provider = Auth::user();

            if($request->has('first_name')) 
                $Provider->first_name = $request->first_name;

            if($request->has('last_name')) 
                $Provider->last_name = $request->last_name;

            if ($request->has('mobile'))
                $Provider->mobile = $request->mobile;

              if ($request->has('exp'))
                $Provider->exp = $request->exp;

            
              if ($request->has('status'))
                $Provider->status = $request->status;

            if ($request->has('description'))
                $Provider->description = $request->description;
  
            if ($request->has('provider_url'))
                $Provider->provider_url = $request->provider_url;
   

            if ($request->hasFile('avatar')) {
                $Provider->avatar = $request->avatar->store('provider/profile');
            }

            if($Provider->profile) {
                $Provider->profile->update([
                        'address' => $request->address ? : $Provider->profile->address,
                    ]);
            } else {
                ProviderProfile::create([
                        'provider_id' => $Provider->id,
                        'address' => $request->address,
                    ]);
            }


            $Provider->save();
            $Provider->avatar='http://www.bestservicepoint.com/storage/app/public/'.$Provider->avatar;
            return $Provider;
        }

        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Provider Not Found!'], 404);
        }
    }

    /**
     * Update latitude and longitude of the user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function location(Request $request)
    {
        $this->validate($request, [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

        if($Provider = \Auth::user()){

            $Provider->latitude = $request->latitude;
            $Provider->longitude = $request->longitude;
            $Provider->save();

            return response()->json(['message' => 'Location Updated successfully!']);

        } else {
            return response()->json(['error' => 'Provider Not Found!']);
        }
    }

    /**
     * Toggle service availability of the provider.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function available(Request $request)
    {
        $this->validate($request, [
                'service_status' => 'required|in:active,offline',
            ]);

        $Provider = Auth::user();
   //dd($Provider->service);
        if($Provider->service) {
            $Provider->service->update(['status' => $request->service_status]);
        } else {
            return response()->json(['error' => 'You account has not been approved for driving']);
        }

        return $Provider;
    }

    /**
     * Update password of the provider.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function password(Request $request)
    {
        if(Setting::get('demo_mode', 0) == 1) {
            return back()->with('flash_error', 'Disabled for demo purposes! Please contact us at info@dragon.com');
        }

        $this->validate($request, [
                'password' => 'required|confirmed',
                'password_old' => 'required',
            ]);

        $Provider = \Auth::user();

        if(password_verify($request->password_old, $Provider->password))
        {
            $Provider->password = bcrypt($request->password);
            $Provider->save();

            return response()->json(['message' => 'Password changed successfully!']);
        } else {
            return response()->json(['error' => 'Please enter correct password'], 422);
        }
    }
}
