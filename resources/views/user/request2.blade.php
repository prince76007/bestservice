
@extends('user.layout.base')

@section('title', 'Dashboard ')

@section('content')
<style>
        /* The container */
        
        .container-checkbox {
            display: block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 22px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        /* Hide the browser's default checkbox */
        
        .container-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }
        /* Create a custom checkbox */
        
        .container-checkbox .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 35px;
            width: 35px;
            border: 1px solid #00a4df;
            border-radius: 100px;
        }
        /* On mouse-over, add a grey background color */
        
        .container-checkbox:hover input ~ .checkmark {
            background-color: #ccc;
        }
        /* When the checkbox is checked, add a blue background */
        
        .container-checkbox input:checked ~ .checkmark {
            background-color: #2196F3;
            border-radius: 100px;
        }
        
        .container-checkbox {
            float: right;
            top: 9px;
        }
        /* Create the checkmark/indicator (hidden when not checked) */
        
        .container-checkbox .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }
        /* Show the checkmark when checked */
        
        .container-checkbox input:checked ~ .checkmark:after {
            display: block;
        }
        /* Style the checkmark/indicator */
        
        .container-checkbox .checkmark:after {
            left: 13px;
            top: 10px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
        }
        /* The container */
        
        .container-radio {
            display: block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 22px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        /* Hide the browser's default radio button */
        
        .container-radio input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }
        /* Create a custom radio button */
        
        .container-radio .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 35px;
            width: 35px;
            background-color: #eee;
            border-radius: 50%;
        }
        /* On mouse-over, add a grey background color */
        
        .container-radio:hover input ~ .checkmark {
            background-color: #ccc;
        }
        /* When the radio button is checked, add a blue background */
        
        .container-radio input:checked ~ .checkmark {
            background-color: #2196F3;
        }
        /* Create the indicator (the dot/circle - hidden when not checked) */
        
        .container-radio .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }
        /* Show the indicator (dot/circle) when checked */
        
        .container-radio input:checked ~ .checkmark:after {
            display: block;
        }
        /* Style the indicator (dot/circle) */
        
        .container-radio .checkmark:after {
            top: 9px;
            left: 9px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: white;
        }
        
        .layout_fullwidth_padding {
            width: 90%;
            margin: 0 5%;
        }
        
        ul.features_list_detailed {
            padding: 0 0 20px 0;
            width: 100%;
            margin: 0px;
            list-style: none;
            float: left;
            clear: both;
        }
        
        ul.features_list_detailed li {
            width: 96%;
            clear: both;
            padding: 17px 2%;
        }
        
        ul.features_list_detailed li {
            padding: 20px 2%;
            margin: 0;
            display: block;
            width: 100%;
            float: left;
            border-bottom: 1px #ddd solid;
        }
        
ul.features_list_detailed li .feat_small_icon {
    width: 27%;
    float: left;
    margin: -2px 0 0 0;
}
.feat_small_icon img{border-radius: 100px;
    height: 82px;}
        
       ul.features_list_detailed li .feat_small_details {
    width: 60%;
    float: left;
    padding: 0 0 0 5%;
}
        
        ul.features_list_detailed li .feat_small_details h4 {
            font-size: 16px;
        }
        
        ul.features_list_detailed li .feat_small_details h4 {
            font-weight: 700;
            font-size: 20px;
            padding: 0 0 5px 0;
            margin: 0px;
        }
        
        ul.features_list_detailed li .feat_small_details a {
            color: #232323;
            text-decoration: none;
        }
        
        #pages_maincontent img {
            display: block;
            max-width: 100%;
        }
        
        .review {
            float: left;
            width: 100%;
        }
        
        .stars-ltr-full {
            color: gold;
        }
        
        .stars-ltr-full span {
            font-size: 22px;
        }
    </style>
<div class="col-md-9">
    <div class="dash-content">
        <div class="row no-margin">
            <div class="col-md-12">
                <h4 class="page-title">@lang('user.ride.ride_now')</h4>
            </div>
        </div>
        @include('common.notify')
		
		
		
		

        <div class="row no-margin">
            <!--div class="col-md-6">
			<!-- THIS IS OLD CODE -->
                
            </div-->
            
			<!--div class="col-md-6">
                <div class="map-responsive">
						  
                </div> 
            </div-->    

            <div class="col-md-12">
 <!-- this code for providr-->
<form action="{{url('create/ride')}}" method="POST" id="" onkeypress="return disableEnterKey(event);">
<div id="pages_maincontent">
<input type="hidden" name="website" value="website">
<input type="hidden" name="s_latitude" id="origin_latitude" value="<?php echo $latitude;?>">
<input type="hidden" name="s_longitude" id="origin_longitude" value="<?php echo $longitude;?>">
<input type="hidden" name="current_longitude" id="long" value="<?php echo $longitude;?>">
<input type="hidden" name="current_latitude" id="lat" value="<?php echo $latitude;?>">
<input type="hidden" name="service_type" value="<?php echo $service_id;?>">
<input type="hidden" name="payment_mode" value="<?php echo 'CASH';?>">
<input type="hidden" name="s_address" value="<?php echo $s_address;?>">
<?php 
	  date_default_timezone_set('Asia/Kolkata');
      $today = date('m/d/Y');
      $time  = date('H:i');	          
      $mins_later30 =  date('H:i',strtotime($time." +1 Mins"));

		//$request->schedule_date = $today;
       // $request->schedule_time = $mins_later30;		
	
?>

<input type="hidden" name="schedule_date" value="<?php echo $schedule_date;?>">
<input type="hidden" name="schedule_time" value="<?php echo $schedule_time;?>">


        <div class="page_single layout_fullwidth_padding">

            <ul class="features_list_detailed">
				 <?php if(!empty($data))
				      {
                       // echo base_url();die;
                    //echo "<pre>";print_r($data);die;
				        foreach($data as $data)
						{
							
					 ?>
		                  
                <li>
                  <!-- main row -->
                  <div class="row">
                    <!-- main div -4 row -->
                    <div class="col-md-4">
                    
                    <div class="feat_small_icon" style="width: 50%;">
<?php if(empty($data->image_of)){?>					
	<img src="http://www.bestservicepoint.com/asset/img/200-200.png" alt="" title="" />
<?php }else{ ?>
  	<img src="<?php echo $data->image_of;?>" alt="" title="" />
	
<?php } ?>
	</div>
                    </div>
                    <div class="col-md-4">

                    <div class="feat_small_details" >
                        <h4><a href="#">
						<?php echo $data->first_name." ".$data->last_name;?>
						</a></h4>
                        <a href="#">Price  ₹
						 <?php echo $data->service_ki_price;?>
						</a>
                        <a href="#">
                            <div class="review">

                                <div class="stars-ltr-full">
                                    <span>★</span>
                                    <span>★</span>
                                    <span>★</span>
                                    <span class="">★</span>
                                    <span>★</span>
                                </div>

                            </div>
                        </a>
                    </div>
                    </div>
                    <div class="col-md-4">
                     <!-- row action option -->
                       <div class="row">
                        <div class="col-md-4" > 
                    <?php if(!empty($data->provider_url)){?>                 
                    <a data-toggle="modal" data-target="#myModal<?php echo $data->id; ?>" >
                            <div class="review">
                                <div class="stars-ltr-full btn btn-warning" style="    color: #fff !important;/*width:1px;padding: 5px 21px; */border-radius: 50%;  background-color: #fe5c7b; border-color: #fe5c7b;">
      
                                    <span><i data-html="true" title="<?php echo 'Url -' .$data->id;?>" style="    color: #fff;" class="fa fa-youtube"  data-toggle="tooltip" data-placement="top" > </i> </span>
                                </div>
                                Youtube
                            </div>
                        </a>
                    <?php } ?>                   
                    </div>
                   
                        <div class="col-md-4">
                          <a href="#">
                            <div class="review">
                                 <div class="stars-ltr-full btn btn-warning" style="    color: #fff !important;/*width:1px;*/ padding: 6px 18px;border-radius: 50%;  background-color: #fe5c7b; border-color: #fe5c7b;">
      
                                    <span ><i data-html="true" style="    color: #fff;" class="fa fa-info"  data-toggle="tooltip" data-placement="top" title="<?php echo "Experience- $data->exp years  <br> About- $data->description";?>"> </i> </span>
                                </div> 
                                {{  $data->uniq_no}}
                                {{  round($data->distance,2)}}km
                             </div>
                        </a>
                    </div>
                                            
                    <div class="feat_small_details1 col-md-4" > 
                     <div class="check-box1">  
                     <label class="container-checkbox">
                      <input type="radio" name="prvdrIdByWeb" value="<?php echo $data->id;?>"  required>
                            <span class="checkmark"></span>
                        </label>
                     </div>
                    </div>

                 </div>
                 <!-- row action option -->
                   </div>
                    <!-- main div -4 row -->

                  </div>
                    <!-- main row -->
                </li>
  <!-- Modal -->
  <div class="modal fade" id="myModal<?php echo $data->id; ?>" role="dialog">
    <div class="modal-dialog">    
      <!-- Modal content-->
      <div class="modal-content" >
        <div class="modal-header"  >
          <button type="button" class="close" data-dismiss="modal">&times;</button>          
        </div>
        <div class="modal-body" >
            <?php if(!empty($data->provider_url)){           
           $data=explode('/',$data->provider_url); 
           if(end($data)!=''){?>

<iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo end($data) ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

  
        

            <?php }}else{ ?>
                 Not Available
            <?php } ?>        
        </div>        
      </div>      
    </div>
  </div>
  <!----------------model end---------------->    


				
		  <?php }}else{?> 
					  Sorry there is no provider available !!!!
                      <?php } ?>
            </ul>
			
    </div>

    </div>
     <?php if(!empty($data)){ ?>
  <button type="submit"  class="half-primary-btn fare-btn">@lang('user.ride.ride_now')</button>	
    <?php } ?>
  <!--button type="button" class="half-secondary-btn fare-btn" data-toggle="modal" data-target="#schedule_modal">Schedule Later</button-->
  </form>
 <!-- end of code -->
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
        <input value="{{date('H:i')}}" type="text" id="timepicker" placeholder="Time" name="schedule_time">

      </div>
      <div class="modal-footer">
        <button type="button" id="schedule_button" class="btn btn-default" data-dismiss="modal">Schedule @lang('main.service')</button>
      </div>

      </form>
    </div>

  </div>
</div>

@endsection

@section('scripts')

    <script type="text/javascript">
        $(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
        
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