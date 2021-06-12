<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Page;
class PagesController extends Controller
{
    //
    function index($id){

       $data['page']=Page::where('id',$id)->first();
        return view('admin.users.pages',$data);
    }

    function update_page(Request $request){
   
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required'
        ]);
        $page = Page::findOrFail($request->id);
        if($request->title!=''){
            $page->title=  $request->title;
        }
        if($request->description!=''){
            $page->description=  $request->description;
        }
        $page->save();
        return redirect()->back()->with('flash_success', 'Page Updated Successfully');    

    }

    function view_pages($id){
        $data['page']=Page::where('id',$id)->first();
        return view('static',$data);
    }
}
