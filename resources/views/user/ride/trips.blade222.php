@extends('user.layout.base')

@section('title', 'My Services ')

@section('content')

<div class="col-md-9">
    <div class="dash-content">
        <div class="row no-margin">
            <div class="col-md-12">
                <h4 class="page-title">@lang('user.my_trips')</h4>
            </div>
        </div>

        <div class="row no-margin ride-detail">
            <div class="col-md-12">
                  
				  
				  
				           <table class="earning-table table table-responsive">
                                <thead>
                                    <tr>
                                        <th>Pickup Time</th>
                                        <th>Service</th>
                                        <th>Pickup Address</th>
                                        <th>Status</th><th><start></th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $fully_sum = 0; ?>
                                @foreach($fully as $each)
                                    <tr>
                                        <td>{{date('Y D, M d - H:i A',strtotime($each->schedule_at))}}</td>
                                        <td>
                                            @if($each->service_type)
                                                {{$each->service_type->name}}
                                            @endif
                                        </td>
                                        <td>
                                            {{$each->s_address}}
                                        </td>
                                        
                                        <td>{{$each->status}}</td>
<td>
	<form action="{{url('update_service_status')}}" method="POST">
	{{ csrf_field() }}
	<input type="hidden" name="id" value="{{$each->id}}">
	<input type="hidden" name="status" value="STARTED">
	  <button class="full-primary-btn fare-btn" onclick="return confirm('Are you sure?')">
	      Start
	  </button>
	</form>
</td>
                                        <td>
	<form action="{{url('update_service_status')}}" method="POST">
	{{ csrf_field() }}
	<input type="hidden" name="id" value="{{$each->id}}">
	<input type="hidden" name="status" value="CANCELLED">
	  <button class="full-primary-btn fare-btn" onclick="return confirm('Are you sure?')">
	      CANCEL
	  </button>
	</form>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
				  
                    <hr>
                    <p style="text-align: center;">No services Available</p>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection