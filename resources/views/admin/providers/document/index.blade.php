@extends('admin.layout.base')

@section('title', 'Provider Documents ')

@section('content')

    <div class="content-area py-1">
        <div class="container-fluid">
            
            <div class="box box-block bg-white">
                <h5 class="mb-1">@lang('main.provider') Service Type Allocation</h5>
                <form action="{{ route('admin.provider.document.store', $Provider->id) }}" method="POST">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-xs-12">
                            @if($ProviderService->count() > 0)
                                
                                <br>
                                <h6>Allocated Services :  </h6>
                                <table class="table table-striped table-bordered dataTable">
                                    <thead>
                                        <tr>
                                            <th>Service Name</th>
                                            <th>Service Charges</th><th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
									
						
									
                                        @foreach($ProviderService as $service)
                                        
                                        <tr><?php //echo "<pre>";print_r($service);?>
                                            <td>{{ $service->service_type->name }}</td>
											
											<td>
											
	   <?php 
	   
	   
		    $select_cus_detail ="SELECT service_ki_price  from provider_services where provider_id = '$service->provider_id' AND
			                      service_type_id ='$service->service_type_id'";
             $data_of_customer  = DB::select($select_cus_detail);
			 //echo "<Pre>";print_r($data_of_customer);
			 echo $data_of_customer[0]->service_ki_price;
			 
			 
			 
		?>	
											
											
											</td>
											
                                            <td>
                                                    <a href="{{route('admin.destory.service',$service->id)}}" class="btn btn-danger btn-large" form="form-delete">Delete</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Service Name</th><th>Service Charges</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                </table>
                               
                            @endif
                            <hr>
                        </div>

                        <div class="col-xs-3">
                            <select class="form-control input" name="service_type" required>
							    <option ="">Select One</option>
                                @forelse($ServiceTypes as $Type)
                                <option value="{{ $Type->id }}">{{ $Type->name }}</option>
                                @empty
                                <option>- Please Create a Service Type -</option>
                                @endforelse
                            </select>
						
                        </div>
	<div class="col-xs-3">
		<input type="text" name="service_ki_price" placeholder="Enter Service Charges" class="form-control input" name="service_type" required>
	</div>	
                        <div class="col-xs-3">
                            <button class="btn btn-primary btn-block" type="submit">Add Service</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="box box-block bg-white">
                <h5 class="mb-1">Provider Documents</h5>
                <table class="table table-striped table-bordered dataTable" id="table-2">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Document Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Provider->documents as $Index => $Document)
                        <tr>
                            <td>{{ $Index + 1 }}</td>
                            <td>{{ $Document->document->name }}</td>
                            <td>{{ $Document->status }}</td>
                            <td>
                                <div class="input-group-btn">
                                    <a href="{{ route('admin.provider.document.edit', [$Provider->id, $Document->id]) }}"><span class="btn btn-success btn-large">View</span></a>
                                    <button class="btn btn-danger btn-large" form="form-delete">Delete</button>
                                    <form action="{{ route('admin.provider.document.destroy', [$Provider->id, $Document->id]) }}" method="POST" id="form-delete">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Document Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
        </div>
    </div>
@endsection