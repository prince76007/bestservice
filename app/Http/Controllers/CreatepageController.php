<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Setting;
use App\StaticPage;
use DB;
use Auth;

class CreatepageController extends Controller
{
	public function createpage(){
		return view('createpage');
	}
	public function storepage(Request $request)
	{
		$staticpage = new StaticPage;
		$staticpage->title = $request->title;
		$staticpage->content = $request->content;
		if($staticpage->save()){
			echo "Data Inserted";
		}

	}
}