@extends('admin.layout.base')

@section('title', 'Add User ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
            <a href="{{ route('admin.user.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> Back</a>

			<h5 style="margin-bottom: 2em;">Add Slider</h5>

            <form class="form-horizontal" action="{{ url('insert_slider') }}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
				
				
				<div class="form-group row">
					<label for="last_name" class="col-xs-12 col-form-label">Description</label>
					<div class="col-xs-10">
<select name="user_type" class="form-control" REQUIRED>	
 <option value=""> Select One </option>
 <option value="USER"> USER </option>
 <option value="PROVIDER"> PROVIDER </option>
</select>			   
				   </div>
				</div>
				
				
				<div class="form-group row">
					<label for="first_name" class="col-xs-12 col-form-label">Title</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="" name="title" required id="first_name" placeholder="Title">
					</div>
				</div>

				<div class="form-group row">
					<label for="last_name" class="col-xs-12 col-form-label">Description</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ old('last_name') }}" name="description" required id="last_name" placeholder="Description">
					</div>
				</div>


				<div class="form-group row">
					<label for="picture" class="col-xs-12 col-form-label">Picture</label>
					<div class="col-xs-10">
						<input type="file" accept="image/*" name="slider_icon" class="dropify form-control-file" required id="picture" aria-describedby="fileHelp">
					</div>
				</div>



				<div class="form-group row">
					<label for="zipcode" class="col-xs-12 col-form-label"></label>
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">Add Slider</button>
						<a href="{{route('admin.user.index')}}" class="btn btn-default">Cancel</a>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>

@endsection
