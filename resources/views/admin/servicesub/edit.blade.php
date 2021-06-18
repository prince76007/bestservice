@extends('admin.layout.base')

@section('title', 'Update Service Type ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
    	    <a href="{{ route('admin.servicesub.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> Back</a>

			<h5 style="margin-bottom: 2em;">Update Sub Service</h5>

            <form class="form-horizontal" action="{{route('admin.servicesub.update', $servicesub->id )}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
            	<input type="hidden" name="_method" value="PATCH">
				<div class="form-group row">
					<label for="name" class="col-xs-2 col-form-label">Sub Service Name</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ $servicesub->name }}" name="name" required id="name" placeholder="Service Name">
					</div>
				</div>
				<div class="form-group row">
					<label for="name" class="col-xs-2 col-form-label">Service Type</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ $servicesub->service_type_id }}" name="service_type_id" required id="service_type_id" placeholder="service_type_id">
					</div>
				</div>

				<div class="form-group row">
					<label for="provider_name" class="col-xs-2 col-form-label">Service Description</label>
					<div class="col-xs-10">
                        <textarea class="form-control" type="text"  name="provider_name" required id="provider_name" placeholder="Service Description">{{ $servicesub->provider_name }}</textarea>
					</div>
				</div>

				<div class="form-group row">
					
					<label for="image" class="col-xs-2 col-form-label">Picture</label>
					<div class="col-xs-10">
					@if(isset($servicesub->image))
                    	<img style="height: 90px; margin-bottom: 15px; border-radius:2em;" src="{{img($servicesub->image)}}">
                    @endif
						<input type="file" accept="image/*" name="image" class="dropify form-control-file" id="image" aria-describedby="fileHelp">
					</div>
				</div>

				<div class="form-group row">
					<label for="fixed" class="col-xs-2 col-form-label">Fixed Base price</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ $servicesub->fixed }}" name="fixed" required id="fixed" placeholder="Fixed">
					</div>
				</div>

				<div class="form-group row">
					<label for="price" class="col-xs-2 col-form-label">Unit Price</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ $servicesub->price }}" name="price" required id="price" placeholder="Price">
					</div>
				</div>


				<div class="form-group row">
					<label for="zipcode" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">Update Service Type</button>
						<a href="{{route('admin.servicesub.index')}}" class="btn btn-default">Cancel</a>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>

@endsection