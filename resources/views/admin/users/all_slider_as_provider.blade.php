@extends('admin.layout.base')

@section('title', 'Users ')

@section('content')

    <div class="content-area py-1">
        <div class="container-fluid">
            
            <div class="box box-block bg-white">
                <h5 class="mb-1">All Slider</h5>
                <a href="{{ route('admin.user.create') }}" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add New @lang('main.user')</a>
                <table class="table table-striped table-bordered dataTable" id="table-2">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Description</th>
 <th>Image</th>                           
						   <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
<?php 					
$select_cus_detail ="SELECT * from slider WHERE user_type ='PROVIDER'";
$data_of_customer  = DB::select($select_cus_detail);
//echo "<Pre>";print_r($data_of_customer);

?>			 
					
                 <?php foreach($data_of_customer as $user)
				 { 
				  $a = 1;
				 ?>
                        <tr>
                           <td>{{$a}} </td>
                            <td>{{$user->title}}</td>
                            <td>{{$user->description}}</td>
                            <td>
<img src="{{ url('/storage/service/'.$user->slider_icon) }}" alt="no" style="width:100px">
                            </td>
	<td>
 <a class="edit_comfirm" style="padding: 12px" href="{{URL::to("edit_slider/".$user->slider_id)}}"><span class="sp1">Edit |</span></a>
 <a class="delete_comfirm" style="padding: 12px" href="{{URL::to("delete_slider/".$user->slider_id)}}"><span class="sp2">Delete </span></a>

	 
	</td>						
                        </tr>
						
				 <?php $a++; } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                                 <th>ID</th>
                            <th>Title</th>
                            <th>Description</th> <th>Image</th>
                            <th>Action</th>

                        </tr>
                    </tfoot>
                </table>
            </div>
            
        </div>
    </div>
	


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript">
    var elems = document.getElementsByClassName('delete_comfirm');
    var confirmIt = function (e) {
        if (!confirm('Are you want to delete?')) e.preventDefault();
    };
    for (var i = 0, l = elems.length; i < l; i++) {
        elems[i].addEventListener('click', confirmIt, false);
    }
</script>

<script type="text/javascript">
    var elems = document.getElementsByClassName('edit_comfirm');
    var confirmIt = function (e) {
        if (!confirm('Are you want to edit?')) e.preventDefault();
    };
    for (var i = 0, l = elems.length; i < l; i++) {
        elems[i].addEventListener('click', confirmIt, false);
    }
</script>	
	
@endsection