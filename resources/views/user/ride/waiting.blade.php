@extends('user.layout.base')

@section('title', 'On Ride')

@section('content')

<div class="col-md-9">
    <div class="dash-content">
    	@include('common.notify')
        
        
@if(Session::has('flash_alert'))
   <script>
       alert("{{ Session::get('flash_alert') }}");
    </script>
@endif
		<div class="row no-margin">
		    <div class="col-md-12">
		        <h4 class="page-title" id="ride_status"></h4>
		    </div>
		</div>
		
		<div class="row no-margin">
		        <div class="col-md-6" id="container" >
		    		<p>Loading...</p>                             
		        </div>

		        <div class="col-md-6">
		            <dl class="dl-horizontal left-right">
		                <dt>@lang('user.request_id')</dt>
		                <dd>{{$request->booking_id}}</dd>
		                <dt>@lang('user.time')</dt>
		                <dd>{{date('d-m-Y H:i A',strtotime($request->assigned_at))}}</dd>
		            </dl> 
		            <div class="user-request-map">
		                <div class="from-to row no-margin">
		                    <div class="from">
		                        <h5>SERVICE LOCATION</h5>
		                        <p>{{$request->s_address}}</p>
		                    </div>
		                   
		                    <div class="type">
		                    	<h5>SERVICE TYPE</h5>
		                        <p>{{$request->service_type->name}}</p>
		                    </div>
		                </div>
		                <?php 
		                    $map_icon = asset('asset/marker.png');
		                    $static_map = "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=600x450&maptype=roadmap&format=png&visual_refresh=true&markers=icon:".$map_icon."%7C".$request->s_latitude.",".$request->s_longitude."&markers=icon:".$map_icon."%7C".$request->d_latitude.",".$request->d_longitude."&path=color:0x191919|weight:8|".$request->s_latitude.",".$request->s_longitude."|".$request->d_latitude.",".$request->d_longitude."&key=".env('GOOGLE_MAP_KEY'); ?>
		                    <div class="map-static" style="background-image: url({{$static_map}});"></div>                               
		            </div>                          
		        </div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
    <script type="text/javascript" src="{{asset('asset/js/rating.js')}}"></script>    
	<script type="text/javascript">
		$('.rating').rating();
		$(document).on('click', '[data-toggle="lightbox"]', function(event) {
		    event.preventDefault();
		    $(this).ekkoLightbox();
		});
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.13.3/react.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.13.3/JSXTransformer.js"></script>

<?php

    $MERCHANT_KEY = "oevZCuqF"; // add your id
    $SALT = "uxptDjtOjI"; // add your id

  // $PAYU_BASE_URL = "https://sandboxsecure.payu.in";
   $PAYU_BASE_URL = "https://secure.payu.in";
    $action = '';
    $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    $posted = array();
	$amount=isset($request->provider_service->service_ki_price)?$request->provider_service->service_ki_price:0;
    $posted = array(
        'key' => $MERCHANT_KEY,
        'txnid' => $txnid,
        'amount' =>$amount,
        'firstname' => Auth::user()->first_name,
        'email' => Auth::user()->email,
        'productinfo' => 'Payment for '.$request->service_type->name,
        'surl' => url('payment-response'),
        'furl' =>url('payment-cancel'),
        'service_provider' => 'payu_paisa',
    );

    if(empty($posted['txnid'])) {
        $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    } 
    else 
    {
        $txnid = $posted['txnid'];
    }

    $hash = '';
    $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
    
    if(empty($posted['hash']) && sizeof($posted) > 0) {
        $hashVarsSeq = explode('|', $hashSequence);
        $hash_string = '';	
        foreach($hashVarsSeq as $hash_var) {
            $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
            $hash_string .= '|';
        }
        $hash_string .= $SALT;

        $hash = strtolower(hash('sha512', $hash_string));
        $action = $PAYU_BASE_URL . '/_payment';
    } 
    elseif(!empty($posted['hash'])) 
    {
        $hash = $posted['hash'];
        $action = $PAYU_BASE_URL . '/_payment';
    }


$name=Auth::user()->first_name;
$email=Auth::user()->email;

?>
<script>
    var hash = '<?php echo $hash ?>';
    function submitPayuForm() {
      if(hash == '') {
        return;
      }
      var payuForm = document.forms.payuForm;
           payuForm.submit();
    }
  </script>
    <script type="text/jsx">
		var MainComponent = React.createClass({
			getInitialState: function () {
                    return {data: [], currency : "{{Setting::get('currency')}}", base_url : "{{asset('storage')}}/app/public/"};
                },
			componentDidMount: function(){
				$.ajax({
			      url: "{{url('status')}}",
			      type: "GET"})
			      .done(function(response){

				        this.setState({
				            data:response.data[0]
				        });

				    }.bind(this));

				    setInterval(this.checkRequest, 5000);
			},
			checkRequest : function(){
				$.ajax({
			      url: "{{url('status')}}",
			      type: "GET"})
			      .done(function(response){
				        this.setState({
				            data:response.data[0]
				        });

				    }.bind(this));
			},
			render: function(){
				return (
					<div>
						<SwitchState checkState={this.state.data} currency={this.state.currency} 
						base_url={this.state.base_url} />
					</div>
				);
			}
		});

		var SwitchState = React.createClass({

			componentDidMount: function() {
				this.changeLabel;
			},

			changeLabel : function(){
				if(this.props.checkState == undefined){
					window.location.reload();
				}else if(this.props.checkState != ""){
					if(this.props.checkState.status == 'SEARCHING'){
						$("#ride_status").text("@lang('user.ride.finding_driver')");
					}else if(this.props.checkState.status == 'STARTED'){
						var provider_name = this.props.checkState.provider.first_name;
						$("#ride_status").text(provider_name+" @lang('user.ride.accepted_rides')");
					}else if(this.props.checkState.status == 'ARRIVED'){
						var provider_name = this.props.checkState.provider.first_name;
						$("#ride_status").text(provider_name+" @lang('user.ride.arrived_ride')");
					}else if(this.props.checkState.status == 'PICKEDUP'){
						$("#ride_status").text("@lang('user.ride.onride')");
					}else if(this.props.checkState.status == 'DROPPED'){
						$("#ride_status").text("@lang('user.ride.waiting_payment')");
					}else if(this.props.checkState.status == 'COMPLETED'){
						var provider_name = this.props.checkState.provider.first_name;
						$("#ride_status").text("@lang('user.ride.rate_and_review') " +provider_name );
					}
					setTimeout(function(){
						$('.rating').rating();
					},400);
				}else{
					$("#ride_status").text('Text will appear here');
				}
			},
			render: function(){

				if(this.props.checkState != ""){

					this.changeLabel();
					if(this.props.checkState.status == 'SEARCHING'){
						return (
							<div>
								<Searching checkState={this.props.checkState} />
							</div>
						);
					}else if(this.props.checkState.status == 'STARTED'){
						return (
							<div>
								<Accepted checkState={this.props.checkState} />
							</div>
						);
					}else if(this.props.checkState.status == 'ARRIVED'){
						return (
							<div>
								<Arrived checkState={this.props.checkState}/>
							</div>
						);
					}else if(this.props.checkState.status == 'PICKEDUP'){
						return (
							<div>
								<Pickedup checkState={this.props.checkState} base_url={this.props.base_url} />
							</div>
						);
                        
					}else if(this.props.checkState.status == 'DROPPED') {
						return (
							<div>
								<DroppedAndPayment checkState={this.props.checkState} currency={this.props.currency} base_url={this.props.base_url} />
							</div>
						);
					}else if((this.props.checkState.status == 'DROPPED') && this.props.checkState.payment_mode == 'CASH' && this.props.checkState.paid == 0){
						return (
							<div>
								<DroppedAndCash checkState={this.props.checkState} currency={this.props.currency} base_url={this.props.base_url} />
							</div>
						);
					}else if((this.props.checkState.status == 'DROPPED' || this.props.checkState.status == 'COMPLETED') && this.props.checkState.payment_mode == 'CARD' && this.props.checkState.paid == 0){
						return (
							<div>
								<DroppedAndCard checkState={this.props.checkState} currency={this.props.currency} base_url={this.props.base_url} />
							</div>
						);
					}else if(this.props.checkState.status == 'COMPLETED'){
						return (
							<div>
								<Review checkState={this.props.checkState} />
							</div>
						);
					}
				}else{
					return ( 
						<p></p>
					 );
				}
			}
		});

		var Searching = React.createClass({
			render: function(){
				return (
					<form action="{{url('cancel/ride')}}" method="POST">
						{{ csrf_field() }}</input>
						<input type="hidden" name="request_id" value={this.props.checkState.id} />
			            <div className="status">
			                <h6>@lang('user.status')</h6>
			                <p>@lang('user.ride.finding_driver')</p>
			            </div>

		            	<button type="submit" className="full-primary-btn fare-btn">@lang('user.ride.cancel_request')</button> 
		            </form>
				);
			}
		});

		var Accepted = React.createClass({
			render: function(){
				return (
					<form action="{{url('cancel/ride')}}" method="POST">
						{{ csrf_field() }}</input>
					<input type="hidden" name="request_id" value={this.props.checkState.id} />
						<div className="status">
			                <h6>@lang('user.status')</h6>
			                <p>@lang('user.ride.accepted_ride')</p>
			            </div>
		            	<button type="submit" className="full-primary-btn fare-btn">@lang('user.ride.cancel_request')</button> 
		            	<br/>
		            		<h5><strong>@lang('user.ride.ride_details')</strong></h5>
		            	<div className="driver-details">
			            	<dl className="dl-horizontal left-right">
				                <dt>@lang('user.driver_name')</dt>
				                <dd>{this.props.checkState.provider.first_name} {this.props.checkState.provider.last_name}</dd>
				                <dt>@lang('user.driver_rating')</dt>
				              
				                <dd>
				                	<div className="rating-outer">
			                            <input type="hidden" value={this.props.checkState.provider.rating} name="rating" className="rating"/>
			                        </div>
				                </dd>
				              {/*  <dt>@lang('user.payment_mode')</dt>
				                <dd>{this.props.checkState.payment_mode}</dd> */}
				            </dl> 
			            </div>

		            </form>
				);
			}
		});

		var Arrived = React.createClass({
			render: function(){
				return (
					<form action="{{url('cancel/ride')}}" method="POST">
						{{ csrf_field() }}</input>
					<input type="hidden" name="request_id" value={this.props.checkState.id} />
						<div className="status">
			                <h6>@lang('user.status')</h6>
			                <p>@lang('user.ride.arrived_ride')</p>
			            </div>
		            	<button type="submit" className="full-primary-btn fare-btn">@lang('user.ride.cancel_request')</button> 
		            	<br/>
		            		<h5><strong>@lang('user.ride.ride_details')</strong></h5>
		            	<div className="driver-details">
			            	<dl className="dl-horizontal left-right">
				                <dt>@lang('user.driver_name')</dt>
				                <dd>{this.props.checkState.provider.first_name} {this.props.checkState.provider.last_name}</dd>
				                <dt>@lang('user.driver_rating')</dt>
				              
				                <dd>
				                	<div className="rating-outer">
			                            <input type="hidden" value={this.props.checkState.provider.rating} name="rating" className="rating"/>
			                        </div>
				                </dd>
				                <dt>@lang('user.payment_mode')</dt>
				                <dd>{this.props.checkState.payment_mode}</dd>
				            </dl> 
			            </div>
		            </form>
				);
			}
		});

		var Pickedup = React.createClass({
			render: function(){
				return (
				<div>
					<div className="status">
		                <h6>@lang('user.status')</h6>
		                <p>@lang('user.ride.onride')</p>
		            </div>
		            <br/>
	            		<h5><strong>@lang('user.ride.ride_details')</strong></h5>
	            	<div className="driver-details">
		            	<dl className="dl-horizontal left-right">
			                <dt>@lang('user.driver_name')</dt>
			                <dd>{this.props.checkState.provider.first_name} {this.props.checkState.provider.last_name}</dd>
			                			                
			                <dt>@lang('user.driver_rating')</dt>
				                <dd>
				                	<div className="rating-outer">
			                            <input type="hidden" value={this.props.checkState.provider.rating} name="rating" className="rating"/>
			                        </div>
				                </dd>
			                <dt>@lang('user.payment_mode')</dt>
			                <dd>{this.props.checkState.payment_mode}</dd>
			                
				            <a href={this.props.base_url + this.props.checkState.before_image} data-toggle="lightbox" data-title="@lang('user.before_image')" data-footer={this.props.checkState.before_comment}>
							    
							    <img src={this.props.base_url + this.props.checkState.before_image} className="before-img img-fluid"/>
							</a>

			            </dl> 
		            </div>
		        </div>
				);
			}
		});

		var DroppedAndPayment = React.createClass({

			render: function(){
				return (
				<div onload="submitPayuForm()">
                <form method="POST" action="{{ $action }}" name="payuForm">
					<div className="status">
		                <h6>@lang('user.status')</h6>
		                <p>@lang('user.ride.dropped_ride')</p>
		            </div>
		            <br/>
		            	<h5><strong>@lang('user.ride.ride_details')</strong></h5>
		            	<dl className="dl-horizontal left-right">
		            		<dt>@lang('user.driver_name')</dt>
			                <dd>{this.props.checkState.provider.first_name} {this.props.checkState.provider.last_name}</dd>
			                
			                <dt>@lang('user.driver_rating')</dt>
			                <dd>
			                	<div className="rating-outer">
		                            <input type="hidden" value={this.props.checkState.provider.rating} name="rating" className="rating"/>
		                        </div>
			                </dd>
		            		

				            <a href={this.props.base_url + this.props.checkState.before_image} data-toggle="lightbox" data-title="@lang('user.before_image')" data-footer={this.props.checkState.before_comment}>
							    
							    <img src={this.props.base_url + this.props.checkState.before_image} className="before-img img-fluid"/>
							</a>

							<a href={this.props.base_url + this.props.checkState.after_image} data-toggle="lightbox" data-title="@lang('user.after_image')" data-footer={this.props.checkState.after_comment}>
							    
							    <img src={this.props.base_url + this.props.checkState.after_image} className="before-img img-fluid"/>
							</a>
				               
                        </dl>
		            	<h5><strong>@lang('user.ride.invoice') </strong></h5>
		            	<dl className="dl-horizontal left-right">
                        
                            <dt>@lang('user.ride.base_price')</dt> 
                            <dd>{this.props.currency} { this.props.checkState.provider_service.service_ki_price}</dd>
                            <dt>@lang('user.ride.tax_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.tax}</dd>
								
								<dt>@lang('user.ride.promotion_applied')</dt>
                            	<dd>{this.props.currency}{this.props.checkState.payment.discount}</dd>  
                            	
                          
                            <dt className="big">@lang('user.ride.amount_paid')</dt>
                            <dd className="big">{this.props.currency}{ (Number(this.props.checkState.provider_service.service_ki_price) + Number(this.props.checkState.payment.tax)) -Number(this.props.checkState.payment.discount)}</dd>
                        </dl>
                        
                        
                         <dd>
                             <input type="hidden" name="key" value="{{ $MERCHANT_KEY }}" /><br />
                              </dd>
            <dd> <input type="hidden" name="hash" value="{{ $hash }}"/></dd>
           <dd> <input type="hidden" name="txnid" value="{{ $txnid }}" /></dd>
           <dd> <input type="hidden" name="amount" value="{{ $amount }}" /></dd>
           <dd> <input type="hidden" name="firstname" id="firstname" value="{{ $name }}" /> </dd>
           <dd> <input type="hidden" name="email" id="email" value="{{ $email }}" /></dd>
          <dd>  <input type="hidden" name="productinfo" value="Payment for {{ $request->service_type->name }}" /></dd>
         <dd>   <input type="hidden" name="surl" value="{{ url('payment-response')}}" /></dd>
          <dd>  <input type="hidden" name="furl" value="{{ url('payment-cancel')}}" /></dd>
        <dd>    <input type="hidden" name="service_provider" value="payu_paisa"  /></dd>
         <dd>
         
      <button type="submit" className="full-primary-btn fare-btn"> PAY ONLINE</button> 
        </dd>
  
                    </form>
		        </div>
				);
			}
		});
        var DroppedAndCash = React.createClass({

			render: function(){
				return (
				<div>
					<div className="status">
		                <h6>@lang('user.status')</h6>
		                <p>@lang('user.ride.dropped_ride')</p>
		            </div>
		            <br/>
		            	<h5><strong>@lang('user.ride.ride_details')</strong></h5>
		            	<dl className="dl-horizontal left-right">
		            		<dt>@lang('user.driver_name')</dt>
			                <dd>{this.props.checkState.provider.first_name} {this.props.checkState.provider.last_name}</dd>
			                
			                <dt>@lang('user.driver_rating')</dt>
			                <dd>
			                	<div className="rating-outer">
		                            <input type="hidden" value={this.props.checkState.provider.rating} name="rating" className="rating"/>
		                        </div>
			                </dd>
		            		<dt>@lang('user.payment_mode')</dt>
                        	<dd>{this.props.checkState.payment_mode}</dd>


				            <a href={this.props.base_url + this.props.checkState.before_image} data-toggle="lightbox" data-title="@lang('user.before_image')" data-footer={this.props.checkState.before_comment}>
							    
							    <img src={this.props.base_url + this.props.checkState.before_image} className="before-img img-fluid"/>
							</a>

							<a href={this.props.base_url + this.props.checkState.after_image} data-toggle="lightbox" data-title="@lang('user.after_image')" data-footer={this.props.checkState.after_comment}>
							    
							    <img src={this.props.base_url + this.props.checkState.after_image} className="before-img img-fluid"/>
							</a>
				               
                        </dl>
		            	<h5><strong>@lang('user.ride.invoice') </strong></h5>
		            	<dl className="dl-horizontal left-right">
                        
                            <dt>@lang('user.ride.base_price')</dt> 
                            <dd>{this.props.currency} {this.props.checkState.provider_service.service_ki_price}</dd>
                            <dt>@lang('user.ride.tax_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.tax}</dd>
                            <dt>@lang('user.ride.distance_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.time_price}</dd>
                            {this.props.checkState.use_wallet ?
								<span>
								<dt>@lang('user.ride.detection_wallet')</dt>
                            	<dd>{this.props.currency}{this.props.checkState.payment.wallet}</dd>  
                            	</span>
                            : ''
                            }
                            {this.props.checkState.payment.discount ?
								<span>
								<dt>@lang('user.ride.promotion_applied')</dt>
                            	<dd>{this.props.currency}{this.props.checkState.payment.discount}</dd>  
                            	</span>
                            : ''
                            }
                            <dt>@lang('user.ride.total')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.total}</dd> 
                            <dt className="big">@lang('user.ride.amount_paid')</dt>
                            <dd className="big">{this.props.currency}{this.props.checkState.payment.total}</dd>
                        </dl>
		        </div>
				);
			}
		});

		var DroppedAndCard = React.createClass({

			render: function(){
				return (
				<div>
					<form method="POST" action="{{url('/payment')}}">
						{{ csrf_field() }}</input>
					<div className="status">
		                <h6>@lang('user.status')</h6>
		                <p>@lang('user.ride.dropped_ride')</p>
		            </div>
		            	<br/>
		            	<h5><strong>@lang('user.ride.ride_details')</strong></h5>
		            	<dl className="dl-horizontal left-right">
		            		<dt>@lang('user.driver_name')</dt>
			                <dd>{this.props.checkState.provider.first_name} {this.props.checkState.provider.last_name}</dd>
			                
			                <dt>@lang('user.driver_rating')</dt>
			                <dd>
			                	<div className="rating-outer">
		                            <input type="hidden" value={this.props.checkState.provider.rating} name="rating" className="rating"/>
		                        </div>
			                </dd>
		            		<dt>@lang('user.payment_mode')</dt>
                        	<dd>{this.props.checkState.payment_mode}</dd>

                        	<a href={this.props.base_url + this.props.checkState.before_image} data-toggle="lightbox" data-title="@lang('user.before_image')" data-footer={this.props.checkState.before_comment}>
							    
							    <img src={this.props.base_url + this.props.checkState.before_image} className="before-img img-fluid"/>
							</a>

							<a href={this.props.base_url + this.props.checkState.after_image} data-toggle="lightbox" data-title="@lang('user.after_image')" data-footer={this.props.checkState.after_comment}>
							    
							    <img src={this.props.base_url + this.props.checkState.after_image} className="before-img img-fluid"/>
							</a>
                        </dl>
		            	<h5><strong>@lang('user.ride.invoice')</strong></h5>
		            	<input type="hidden" name="request_id" value={this.props.checkState.id} />
		            	<dl className="dl-horizontal left-right">
                           <dt>@lang('user.ride.base_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.fixed}</dd>
                            <dt>@lang('user.ride.tax_price') </dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.tax}</dd>
                            <dt>@lang('user.ride.distance_price')</dt>
                            <dd>{this.props.currency}{this.props.checkState.payment.time_price}</dd>
                            
                            <dt>@lang('user.ride.total')</dt>
                            {this.props.checkState.use_wallet ?
								<span>
								<dt>@lang('user.ride.detection_wallet')</dt>
                            	<dd>{this.props.currency}{this.props.checkState.payment.wallet}</dd>  
                            	</span>
                            : ''
                            }
                            {this.props.checkState.payment.discount ?
								<span>
								<dt>@lang('user.ride.promotion_applied')</dt>
                            	<dd>{this.props.currency}{this.props.checkState.payment.discount}</dd>  
                            	</span>
                            : ''
                            }
                            <dd>{this.props.currency}{this.props.checkState.payment.total}</dd> 
                            <dt className="big">@lang('user.ride.amount_paid')</dt>
                            <dd className="big">{this.props.currency}{this.props.checkState.payment.total}</dd>
                        </dl>
                    	<button type="submit" className="full-primary-btn fare-btn">CONTINUE TO PAY</button>   
                    </form>
		        </div>
				);
			}
		});

		var Review = React.createClass({
			render: function(){
				return (
				<form method="POST" action="{{url('/rate')}}">
				{{ csrf_field() }}</input>
                    <div className="rate-review">
                        <label>@lang('user.ride.rating')</label>
                        <div className="rating-outer">
                            <input type="hidden" value="1" name="rating" className="rating"/>
                        </div>
						<input type="hidden" name="request_id" value={this.props.checkState.id} />
                        <label>@lang('user.ride.comment')</label>
                        <textarea className="form-control" name="comment" placeholder="Write Comment"></textarea>
                    </div>
                    <button type="submit" className="full-primary-btn fare-btn">SUBMIT</button>   
                </form>
				);
			}
		});

		React.render(<MainComponent/>,document.getElementById("container"));
	</script>
 

<script>
      $( document ).ready(function() {
    bootbox.alert("Your message here???");
});
  </script>
@endsection