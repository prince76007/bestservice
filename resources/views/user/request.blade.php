@extends('user.layout.base')

@section('title', 'Dashboard ')

@section('content')

<div class="col-md-9">
    <div class="dash-content">
        <div class="row no-margin">
            <div class="col-md-12">
                <h4 class="page-title">@lang('user.ride.ride_now')</h4>
            </div>
        </div>
        @include('common.notify')
		
		
		
		

        <div class="row no-margin">
            <div class="col-md-6">
			
			<!-- THIS IS OLD CODE -->
                <!--form action="{{url('create/ride')}}" method="POST" id="create_ride" onkeypress="return disableEnterKey(event);"-->
<form action="{{url('create/ride2')}}" method="POST" id="create_ride" onkeypress="return disableEnterKey(event);">	
				
                 {{ csrf_field() }}
                <div class="input-group dash-form">
                    <input type="text" id="pac-input" class="form-control" name="s_address"  placeholder="Enter Service location">
                </div>
                <input type="hidden" name="website" value="website">
                <input type="hidden" name="s_latitude" id="origin_latitude">
                <input type="hidden" name="s_longitude" id="origin_longitude">
                <input type="hidden" name="current_longitude" id="long">
                <input type="hidden" name="current_latitude" id="lat">
                 <input type="hidden" name="service_type" value="@if(Request::has('service')){{Request::get('service')}}@endif">
                 <hr>
                <dl class="dl-horizontal left-right" >
                    <dt>Service Type</dt>
                    <dd>{{ $service->name }}</dd>
                    <dt>Service Rate</dt>
                    <dd> {{ currency($service->fixed)  }}-{{ currency($service->price)}}</dd>
                    <dt style="width: 200px;" data-toggle="modal" data-target="#terms_modal"> <span style="border-bottom: 1px dotted;">Accept Terms & Condition ? 
                    </span></dt> 
                    <dd> <input type="checkbox" name="terms"  id="terms" required checked ></dd>
                    @if(Auth::user()->wallet_balance > 0)

                        <input type="checkbox" name="use_wallet" value="1"><span style="padding-left: 15px;">@lang('user.use_wallet_balance')</span>
                        <br>
                        <br>
                            <dt>@lang('user.available_wallet_balance')</dt>
                            <dd>{{currency(Auth::user()->wallet_balance)}}</dd>
                        @endif
                </dl>
<input type="hidden" name="payment_mode" value="CASH">
                 <!-- <p>@lang('user.payment_method')</p> -->
                    <!--<select class="form-control" name="payment_mode" id="payment_mode" onchange="card(this.value);">
                      <option value="CASH">CASH</option>
                      @if(Setting::get('CARD') == 1)
                      @if($cards->count() > 0)
                        <option value="CARD">CARD</option>
                      @endif
                      @endif
                    </select-->
                    <br>

                    @if(Setting::get('CARD') == 1)
                        @if($cards->count() > 0)
                        <select class="form-control" name="card_id" style="display: none;" id="card_id">
                          <option value="">Select Card</option>
                          @foreach($cards as $card)
                            <option value="{{$card->card_id}}">{{$card->brand}} **** **** **** {{$card->last_four}}</option>
                          @endforeach
                        </select>
                        @endif
                    @endif
                <!--button type="submit"  class="half-primary-btn fare-btn">@lang('user.ride.ride_now')</button-->
                <button type="button" class="half-secondary-btn fare-btn" data-toggle="modal" data-target="#schedule_modal">Book Now</button>
                </form>
            </div>
            <div class="col-md-6">
                <div class="map-responsive">
                    <div id="map" style="width: 100%; height: 450px;"></div>
                </div> 
            </div>
        </div>

    </div>
</div>


<!-- Schedule Modal -->
<div id="schedule_modal" class="modal fade schedule-modal" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Schedule a @lang('main.service')</h4>
      </div>
      <form>
      <div class="modal-body">
        
        <label>Date</label>
        <input value="{{date('m/d/Y')}}" type="text" id="datepicker" placeholder="Date" name="schedule_date">
        <label>Time</label>
          <?php 
          $selectedTime=date('H:i');
          $endTime = strtotime("+5 minutes", strtotime($selectedTime));
//echo date('h:i:s', $endTime);
          ?>
        <input value="{{date('H:i',$endTime)}}" type="text" id="timepicker" placeholder="Time" name="schedule_time">

      </div>
      <div class="modal-footer"> 
        <button type="button" id="schedule_button" class="btn btn-default" data-dismiss="modal">Schedule @lang('main.service')</button>
      </div>

      </form>
    </div>

  </div>
</div>


<div id="schedule_modal" class="modal fade schedule-modal" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Schedule a @lang('main.service')</h4>
      </div>
      <form>
      <div class="modal-body">
        
        <label>Date</label>
        <input value="{{date('m/d/Y')}}" type="text" id="datepicker" placeholder="Date" name="schedule_date">
        <label>Time</label>
          <?php 
          $selectedTime=date('H:i');
          $endTime = strtotime("+5 minutes", strtotime($selectedTime));
//echo date('h:i:s', $endTime);
          ?>
        <input value="{{date('H:i',$endTime)}}" type="text" id="timepicker" placeholder="Time" name="schedule_time">

      </div>
      <div class="modal-footer">
        <button type="button" id="schedule_button" class="btn btn-default" data-dismiss="modal">Schedule @lang('main.service')</button>
      </div>

      </form>
    </div>

  </div>
</div>


<div id="terms_modal" class="modal fade schedule-modal" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">You Confirm that you are in the agreement with and bound by terms and condition</h4>
      </div>

      <div class="modal-body">
        <p> Payment Terms</p>
        <p>i. Payments are non-refundable. </p>
        <p>ii. Payments of invoice is required no later than 14 days from the date of the invoice unless by prior arrangement with best service. </p>
        <p>iii. If account are not setteled or best service has not been contacted regarding the delay, access to the related website may be denied and web pages removed. </p>
        <p>iv. Any additionsl costs including admin, emails phone calls or faxes or third party charges will be added to the accout in question. </p>
      </div>
      <div class="modal-footer">
        <button type="button" id="schedule_button" class="btn btn-default" data-dismiss="modal"> Ok </button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')

    <script type="text/javascript">
        $(document).ready(function(){
            $('#schedule_button').click(function(){
                $("#datepicker").clone().attr('type','hidden').appendTo($('#create_ride'));
                $("#timepicker").clone().attr('type','hidden').appendTo($('#create_ride'));
                document.getElementById('create_ride').submit();
            });
        });
    </script>
    
    <script type="text/javascript">
        $('#datepicker').datepicker();
         $('#timepicker').timepicker({showMeridian : false});
    </script>

    
    <script type="text/javascript">
        var current_latitude = 13.0574400;
        var current_longitude = 80.2482605;
    </script>

    <script type="text/javascript">

    if( navigator.geolocation )
    {
       navigator.geolocation.getCurrentPosition( success, fail );
    }
    else
    {
        console.log('Sorry, your browser does not support geolocation services');
        initAutocomplete();
    }

     function success(position)
     {
         document.getElementById('long').value = position.coords.longitude;
         document.getElementById('lat').value = position.coords.latitude

        if(position.coords.longitude != "" && position.coords.latitude != ""){
          current_longitude = position.coords.longitude;
          current_latitude = position.coords.latitude;
        }
        initAutocomplete();
     }

     function fail()
     {
        // Could not obtain location
        console.log('unable to get your location');
        initAutocomplete();
     }

   </script> 
   <script type="text/javascript" src="{{asset('asset/js/service.js')}}"></script>
    <!-- <script type="text/javascript" src="{{asset('asset/js/map.js')}}"></script> -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_KEY')}}&libraries=places&callback=initAutocomplete"
        async defer></script>

    <script type="text/javascript">
        function disableEnterKey(e)
        {
             var key;      
             if(window.e)
                  key = window.e.keyCode; //IE
             else
                  key = e.which; //firefox      

            if(key == 13){
                return e.preventDefault();
                console.log('asdasd');
            }

        }
    </script>

     <script type="text/javascript">
        function card(value){
            if(value == 'CARD'){
                $('#card_id').fadeIn(300);
            }else{
                $('#card_id').fadeOut(300);
            }
        }
    </script>
 
@endsection