@extends('user.layout.auth')

@section('title', 'Create New Page ')

@section('content')
<div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-clock-o fa-fw"></i><strong>Enter The Details of Teacher</strong>
                        </div>
                        <!-- /.panel-heading -->
                         <div class="panel-body" style="padding-left: 4%;">
  
  
  
 

  <form method="post" action="{{route('storepage')}}" enctype="multipart/form-data">
    <div class="form-group">
      
      {{ csrf_field() }}

     
     
      </div>
      <div class="form-group">
      <label for="Name of Teacher">Title of Page</label>
      <input type="text" name="title" class="form-control" >
      @if($errors->has('title'))
      @foreach($errors->get('title') as $error)
      <div class="alert alert-danger alert-dismissible fade show mt-2">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
      <p>{{$error}}</p>
    </div>
      @endforeach
      @endif

      </div>
      
      
    <div class="form-group">
      <label>Page Content</label>
      <textarea name="content" class="form-control"></textarea>
      @if($errors->has('content'))
      @foreach($errors->get('content') as $error)
      <div class="alert alert-danger alert-dismissible fade show  mt-2">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
      <p>{{$error}}</p>
    </div>
      @endforeach
      @endif
      </div>
     
      
      <input type="submit" value="Submit">
      
    
  </form>
  </div>
  </div>
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script>
	CKEDITOR.replace( 'content', {
		height:400
	} );
</script>


@endsection