
@extends('admin.layout.base')

@section('title', 'Update Pages ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
    	    

			<h5 style="margin-bottom: 2em;">Update {{$page->title}}</h5>

            <form class="form-horizontal" action="{{url('admin/update_page')}}" method="POST" >
            	{{csrf_field()}}
            	<input type="hidden" name="_method" value="PATCH">
				<div class="form-group row">
					<label for="first_name" class="col-xs-2 col-form-label">Title</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{$page->title}}" name="title" required id="title" placeholder="Page Tile">
                        <input class="form-control" type="hidden" value="{{$page->id}}" name="id" required id="id" placeholder="Page Tile">
					</div>
				</div>
                <div class="form-group row">
					<label for="first_name" class="col-xs-2 col-form-label">Description</label>
					<div class="col-xs-10">
						<textarea class="form-control"  name="description" required id="description" placeholder="Page Description">{{$page->description}}</textarea>
					</div>
				</div>

				
				<div class="form-group row">
					<label for="zipcode" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">Update Page</button>
						<a href="{{route('admin.user.index')}}" class="btn btn-default">Cancel</a>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script>
	CKEDITOR.replace( 'description', {
		height:400
	} );
</script>
@endsection
