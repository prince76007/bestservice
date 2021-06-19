<?php

namespace App\Http\Controllers\Resource;

use App\ServiceSubType;
use App\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use Exception;
use Storage;
use Setting;

class ServiceSubResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $servicesub = ServiceSubType::all();
        foreach($servicesub as $key=>$sub){
        $parentservice=ServiceType::where('id',$sub->service_type_id)->first();
        $servicesub[$key]['service_type_name']=$parentservice['name'];
        }
        if($request->ajax()) {
            return $servicesub;
        } else {
            return view('admin.servicesub.index', compact('servicesub'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parentservices=ServiceType::all();
        return view('admin.servicesub.create',compact('parentservices'));
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
            'name' => 'required|max:255',
            'service_type_id'=>'required',
            'provider_name' => 'required|max:255',
            'fixed' => 'required|numeric',
            'price' => 'required|numeric',
            'image' => 'mimes:ico,png,jpg,jpeg'
        ]);

        try{

            $services_sub = $request->all();
            
            if ($request->hasFile('image')) {
                $services_sub['image'] = $request->image->store('service');
            }

            $services_sub = ServiceSubType::create($services_sub);

            return back()->with('flash_success','Service Sub Type Saved Successfully');

        } 

        catch (Exception $e) {
            return back()->with('flash_errors', 'Service Sub Type Not Found');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return ServiceSubType::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $e;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            //$masterArray=array();
            $parentservices=ServiceType::all();
            $services_sub = ServiceSubType::findOrFail($id);
            return view('admin.servicesub.edit',compact('services_sub'),compact('parentservices'));
        } catch (ModelNotFoundException $e) {
            return $e;
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
        if(Setting::get('demo_mode', 0) == 1) {
            return back()->with('flash_error', 'Disabled for demo purposes! Please contact us at info@dragon.com');
        }

        $this->validate($request, [
            'name' => 'required|max:255',
            'service_type_id'=>'required',
            'provider_name' => 'required|max:255',
            'fixed' => 'required|numeric',
            'price' => 'required|numeric',
            'image' => 'mimes:ico,png,jpeg,jpg'
        ]);

        try {

            $servicesub = ServiceSubType::findOrFail($id);

            if ($request->hasFile('image')) {
                $servicesub->image  = $request->image->store('servicesub');
            }

            $servicesub->name = $request->name;
            $servicesub->provider_name = $request->provider_name;
            $servicesub->fixed = $request->fixed;
            $servicesub->price = $request->price;
            $servicesub->save();

            return redirect()->route('admin.servicesub.index')->with('flash_success', 'Service Sub Type Updated Successfully');    
        } 

        catch (ModelNotFoundException $e) {
            return back()->with('flash_errors', 'Service Sub Type Not Found');
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
        try {
            ServiceSubType::find($id)->delete();
            return back()->with('message', 'Service Sub Type deleted successfully');
        } 
        catch (Exception $e) {
            return back()->with('flash_errors', 'Service Sub Type Not Found');
        }
    }
}
