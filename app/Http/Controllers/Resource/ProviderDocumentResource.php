<?php

namespace App\Http\Controllers\Resource;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SendPushNotification;

use Setting;
use App\Provider;
use App\ServiceType;
use App\ProviderService;
use App\ProviderDocument;
use DB;

class ProviderDocumentResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $provider)
    {
        try {
            $Provider = Provider::findOrFail($provider);
            $ProviderService = ProviderService::where('provider_id',$provider)->with('service_type')->get();
            $ServiceTypes = ServiceType::all();
            return view('admin.providers.document.index', compact('Provider', 'ServiceTypes','ProviderService'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $provider)
    { 
	echo $request->service_type;
	echo "<br>";
	echo $request->service_ki_price;
        if(Setting::get('demo_mode', 0) == 1) {
            return back()->with('flash_error', 'Disabled for demo purposes! Please contact us at info@dragon.com');
        }

        $this->validate($request, [
                'service_type' => 'required|exists:service_types,id',
            ]);

        try {
            $ProviderService = ProviderService::where('provider_id', $provider)
                                ->where('service_type_id', $request->service_type)
                                ->firstOrFail();

            $ProviderService->update([
                    'service_type_id' => $request->service_type,
                    'status' => 'offline',
					'service_ki_price' => $request->service_ki_price
                ]);
				

            // sending push to the provider

            (new SendPushNotification)->DocumentsVerfied($provider);

        } catch (ModelNotFoundException $e) {
            ProviderService::create([
                    'provider_id' => $provider,
                    'service_type_id' => $request->service_type,
                    'status' => 'offline',
					
                ]);
        }
DB::table('provider_services')->where(['provider_id'=>$provider,'service_type_id'=>$request->service_type])
     ->update(array('service_ki_price'=> $request->service_ki_price));
        return redirect()->route('admin.provider.document.index', $provider)->with('flash_success', 'Hey admin  Provider service type updated successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($provider, $id)
    {
        try {
            $Document = ProviderDocument::where('provider_id', $provider)
                ->where('id', $id)
                ->firstOrFail();

            return view('admin.providers.document.edit', compact('Document'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $provider, $id)
    {
        if(Setting::get('demo_mode', 0) == 1) {
            return back()->with('flash_error', 'Disabled for demo purposes! Please contact us at info@dragon.com');
        }

        try {

            $Document = ProviderDocument::where('provider_id', $provider)
                ->where('id', $id)
                ->firstOrFail();
            $Document->update(['status' => 'ACTIVE']);

            return redirect()->route('admin.provider.document.index', $provider)->with('flash_success', 'Provider document has been approved.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.provider.document.index', $provider)->with('flash_error', 'Provider not found!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($provider, $id)
    {
        try {

            $Document = ProviderDocument::where('provider_id', $provider)
                ->where('id', $id)
                ->firstOrFail();
            $Document->delete();

            return redirect()->route('admin.provider.document.index', $provider)->with('flash_success', 'Provider document has been deleted');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.provider.document.index', $provider)->with('flash_error', 'Provider not found!');
        }
    }
}
