@extends('user.layout.base')

@section('title', 'Upcoming Trips ')

@section('content')

<div class="col-md-9">
    <div class="dash-content">
        <div class="row no-margin">
            <div class="col-md-12">
                <h4 class="page-title">@lang('user.upcoming_trips')</h4>
            </div>
        </div>

        <div class="row no-margin ride-detail">
            <div class="col-md-12">
            @if($trips->count() > 0)

                <table class="table table-condensed" style="border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>BookingID</th>
                            <th>@lang('user.schedule_date')</th>
                            <th>@lang('user.type')</th>
                            <!--th>@lang('user.payment')</th-->
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($trips as $trip)

                        <tr data-toggle="collapse" data-target="#trip_{{$trip->id}}" class="accordion-toggle collapsed">
                            <td><span class="arrow-icon fa fa-chevron-right"></span></td>
                            <td>{{$trip->booking_id}}</td>
                            <td>{{date('d-m-Y H:i:s',strtotime($trip->schedule_at))}}</td>
                            @if($trip->service_type)
                            <td>{{$trip->service_type->name}}</td>
                             @else
                                <td>-</td>
                            @endif
                            <!--td>@lang('user.paid_via') {{$trip->payment_mode}}</td-->
                        </tr>
                        <tr class="hiddenRow">
                            <td colspan="6">
                                <div class="accordian-body collapse row" id="trip_{{$trip->id}}">
                                    <div class="col-md-6">
                                        <div class="my-trip-left">
                                        <?php 
                                    $map_icon = asset('asset/marker.png');
                                    $static_map = "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=600x450&maptype=roadmap&format=png&visual_refresh=true&markers=icon:".$map_icon."%7C".$trip->s_latitude.",".$trip->s_longitude."&key=".env('GOOGLE_MAP_KEY'); ?>

                                            <div class="map-static" style="background-image: url({{$static_map}});">
                                                
                                            </div>
                                            <div class="from-to row no-margin">
                                                <div class="from">
                                                    <h5>@lang('user.from')</h5>
                                                    <p>{{$trip->s_address}}</p>
                                                </div>
                                                <div class="to">
                                                    <h5>@lang('user.to')</h5>
                                                    <p>{{$trip->d_address}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">

                                        <div class="mytrip-right">
                                             <h5>@lang('main.provider') Details </h5>
                                             <div class="trip-user">
                                             <?php if($trip->provider->avatar==''){
                                                $img=img($trip->provider->avatar);
                                             }else{
                                                $img=img('app/public/'.$trip->provider->avatar);
                                             }
                                             ?>
                                                <div class="user-img" style="background-image: url({{ $img}});">
                                                </div>
                                                <div class="user-right">
                                                    @if($trip->provider)
                                                    <h5>{{$trip->provider->first_name}} {{$trip->provider->last_name}}</h5>
                                                     @else
                                                        <h5>-</h5>
                                                    @endif
                                                    <p>{{$trip->status}}</p>
                                                </div>
                                            </div>

                                            <div class="fare-break">

                                               <form method="POST" action="{{url('cancel/ride')}}">
                                                  {{ csrf_field() }}
                                                     <input type="hidden" name="request_id" value="{{$trip->id}}" />
                                                   <button class="full-primary-btn fare-btn" type="submit">Cancel @lang('main.service')</button>
                                               </form>

                                            </div>


                                        </div>

                                    </div>

                                </div>
                            </td>
                        </tr>

                        @endforeach


                    </tbody>
                </table>
                @else
                    <hr>
                    <p style="text-align: center;">No @lang('main.service')s Available</p>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection