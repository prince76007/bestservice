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
                                        <th>BookingId</th>
                                        <th>Service</th>
                                        <th>Pickup Address</th>
                                        <th>Status</th><th>Provider</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $fully_sum = 0; ?>
                                @foreach($fully as $each)
                                    <tr>
                                      <td>{{date('Y D, M d - H:i A',strtotime($each->schedule_at))}}
										</td>
                                        
                                        <td>{{$each->booking_id}}</td>
										  <td>{{$each->service_name}}</td>
										<td>{{$each->s_address}}</td>
										<td>{{$each->status}}</td>
										<td>{{$each->first_name}}-{{$each->last_name}}</td>
									
<td>
	<form action="{{url('update_service_statusBYuser')}}" method="POST">
	{{ csrf_field() }}
	<input type="hidden" name="id" value="{{$each->id}}">
	<input type="hidden" name="status" value="CANCELLED">
	  <button class="full-primary-btn fare-btn" onclick="return confirm('Are you sure?')">
	      Cancle
	  </button>
	</form>
</td>

                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
				  
                    <hr>
					<?php if(empty($fully)){?>
                    <p style="text-align: center;">No services Available</p>
					<?php } ?>
           
            </div>
        </div>

    </div>
</div>
@endsection