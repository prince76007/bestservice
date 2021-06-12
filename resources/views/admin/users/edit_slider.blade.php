@extends('admin.layout.base')

@section('title', 'Add User ')

@section('content')
 
<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
            <a href="{{ route('admin.user.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> Back</a>

			<h5 style="margin-bottom: 2em;">Edit Slider</h5>

            <form class="form-horizontal" action="{{ url('update_slider') }}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
				
<?php foreach($data as $dat){?>				
				<div class="form-group row">
					<label for="first_name" class="col-xs-12 col-form-label">Title</label>
					<div class="col-xs-10">
					    <input type="hidden" name="slider_id" value="<?php echo $dat->slider_id;?>">
						<input class="form-control" type="text" value="<?php echo $dat->title;?>" name="title" required id="first_name" placeholder="Title">
					</div>
				</div>

				<div class="form-group row">
					<label for="last_name" class="col-xs-12 col-form-label">Description</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="<?php echo $dat->description;?>" name="description" required id="last_name" placeholder="Description">
					</div>
				</div>
				
				


				<div class="form-group row">
					<label for="picture" class="col-xs-12 col-form-label">Picture</label>
					<div class="col-xs-10">
						<input type="file" accept="image/*" name="slider_icon" class="dropify form-control-file"  id="picture" aria-describedby="fileHelp">
					</div>
				</div>
<select name="user_type" class="form-control" REQUIRED>	
 <option value=""> Select One </option>
 <option value="USER"  <?=($dat->user_type == 'USER')?"selected":""?>> USER </option>
 <option value="PROVIDER"  <?=($dat->user_type == 'PROVIDER')?"selected":""?>> PROVIDER </option>
</select>					
				
				
<div class="form-group row">
  <label for="picture" class="col-xs-12 col-form-label">Image</label>
  <div class="col-xs-10">
      <img src="{{ url('/storage/service/'.$dat->slider_icon) }}" style="width:200px">
  </div>
</div>					

<?php } ?>

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
