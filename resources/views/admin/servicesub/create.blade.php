@extends('admin.layout.base')

@section('title', 'Add Service Type ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
            <a href="{{ route('admin.servicesub.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> Back</a>

			<h5 style="margin-bottom: 2em;">Add Service Sub Type</h5>

            <form class="form-horizontal" action="{{route('admin.servicesub.store')}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
				<div class="form-group row">
					<label for="name" class="col-xs-12 col-form-label">Service Name</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ old('name') }}" name="name" required id="name" placeholder="Service Name">
					</div>
				</div>
				<div class="form-group row">
				<label for="service_type_id" class="col-xs-12 col-form-label">Parent Service</label>
				<div class="col-xs-10">
				<select class="form-control input" name="service_type_id" required id="service_type_id">
							    <option ="">Select Parent Type</option>
                                @forelse($parentservices as $parentType)
                                <option value="{{ $parentType->id }}">{{ $parentType->name }}</option>
                                @empty
                                <option>- Please Create a Service Type -</option>
                                @endforelse
                            </select>
							</div>
					<!-- <label for="service_type_id" class="col-xs-12 col-form-label">Parent Service</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ old('service_type_id') }}" name="name" required id="service_type_id" placeholder="Service Name">
					</div> -->
				</div>
				<div class="form-group row">
					<label for="provider_name" class="col-xs-12 col-form-label">Service Description </label>
					<div class="col-xs-10">
                        <textarea class="form-control" type="text" value="{{ old('provider_name') }}" name="provider_name" required id="provider_name" placeholder="Service Description"></textarea>
					</div>
				</div>

				<div class="form-group row">
					<label for="picture" class="col-xs-12 col-form-label">Service Image</label>
					<div class="col-xs-10">
						<input type="file" accept="image/*" name="image" class="dropify form-control-file" id="picture" aria-describedby="fileHelp">
					</div>
				</div>

				<div class="form-group row">
					<label for="fixed" class="col-xs-12 col-form-label">Fixed Base Price</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ old('fixed') }}" name="fixed" required id="fixed" placeholder="Fixed">
					</div>
				</div>

				<div class="form-group row">
					<label for="price" class="col-xs-12 col-form-label">Unit Price</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ old('price') }}" name="price" required id="price" placeholder="Price">
					</div>
				</div>

				<div class="form-group row">
					<label for="zipcode" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">Add Service Sub Type</button>
						<a href="{{route('admin.servicesub.index')}}" class="btn btn-default">Cancel</a>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>

@endsection
