@extends('admin.layout.base')

@section('title', 'Service Sub Types ')

@section('content')

    <div class="content-area py-1">
        <div class="container-fluid">
            
            <div class="box box-block bg-white">
                <h5 class="mb-1">Service Sub Types</h5>
                <a href="{{ route('admin.servicesub.create') }}" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add New Sub Service</a>
                <table class="table table-striped table-bordered dataTable" id="table-2">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Service Parent Type</th>
                            <th>Service Name</th>
                            <th>Service Description </th>
                            <th>Fixed Base Price</th>
                            <th>Unit Price</th>
                            <th>Service Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($servicesub as $index => $sub_service)
                        <tr>
                            <td>{{$index + 1}}</td>
                            <td>{{$sub_service->service_type_id}}</td>
                            <td>{{$sub_service->name}}</td>
                            <td>{{$sub_service->provider_name}}</td>
                            <td>{{currency($sub_service->fixed)}}</td>
                            <td>{{currency($sub_service->price)}}</td>
                            <td>
                                @if($sub_service->image) 
                                    <img src="{{img($sub_service->image)}}" style="height: 50px" >
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.servicesub.destroy', $sub_service->id) }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="DELETE">
                                    <a href="{{ route('admin.servicesub.edit', $sub_service->id) }}" class="btn btn-info"><i class="fa fa-pencil"></i> Edit</a>
                                    <button class="btn btn-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i> Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>ID</th>
                            <td>Service Parent Type</td>
                            <th>Service Name</th>
                            <th>Service Description </th>
                            <th>Fixed Base Price</th>
                            <th>Unit Price</th>
                            <th>Service Image</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
        </div>
    </div>
@endsection