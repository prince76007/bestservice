@extends('user.layout.auth')

@section('content')

<?php $login_user = asset('asset/img/login-user-bg.jpg'); ?>
<div class="full-page-bg" style="background-image: url({{$login_user}});">
<div class="log-overlay"></div>
    <div class="full-page-bg-inner">
        <div class="row no-margin">
            <div class="col-md-6 log-left">
                <span class="login-logo"><img src="../../../asset/img/200-200.png"></span>
                <h2>Create your account and get moving in minutes</h2>
                <p>Welcome to {{Setting::get('site_title','Tranxit')}}, the easiest way to get around at the tap of a button.</p>
            </div>
            <div class="col-md-6 log-right">
                <div class="login-box-outer">
                <div class="login-box row no-margin">
                    <div class="col-md-12">
                        <!-- <a class="log-blk-btn" href="{{url('login')}}">ALREADY HAVE AN ACCOUNT?</a> -->
                        <h3> Account Number Verify</h3>
                    </div>
                    <form role="form" method="POST" action="{{ url('/otpsubmit') }}">
                        {{ csrf_field() }}

                   

                        <div class="col-md-12">
                            <input type="text" autofocus class="form-control" placeholder="Mobile" name="otp" value="{{ phone_formate($mobile) }}" readonly="">

                            @if ($errors->has('otp'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('otp') }}</strong>
                                </span>
                            @endif
                        </div>


                        <div class="col-md-12">
                            <input type="text" autofocus class="form-control" placeholder="Enter Your Opt" name="otp" value="">

                            @if ($errors->has('otp'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('otp') }}</strong>
                                </span>
                            @endif
                        </div>
                        
                        <div class="col-md-12">
                                <button class="log-teal-btn" type="submit">Submit</button>
                        </div>
                    </form>     

                    <div class="col-md-12">
                        <p id="demo"></p>
                         <!-- <p class="helper">Or <a href="{{url('/resendotp')}}">Reset OTP</a> .</p>    -->
                    </div>
         
                    <div class="col-md-12">
                        <p class="helper"><a href="{{url('/logout')}}">Cancel</a> </p>
                    </div>
                </div>


                <div class="log-copy"><p class="no-margin">&copy;{{date('Y')}} {{Setting::get('site_title','Tranxit')}}</p></div>
                </div>
            </div>
        </div>
    </div>

<script>
var nowtime='@php echo custom_current_add_date_time(); @endphp' ;   
// Set the date we're counting down to
var countDownDate = new Date(nowtime).getTime();

// Update the count down every 1 second
var x = setInterval(function() {

  // Get today's date and time
  var now = new Date().getTime();
    
  // Find the distance between now and the count down date
  var distance = countDownDate - now;
    
  // Time calculations for days, hours, minutes and seconds
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
  // Output the result in an element with id="demo"
  document.getElementById("demo").innerHTML = minutes + "m " + seconds + "s ";
    
  // If the count down is over, write some text 
  if (distance < 0) {
    clearInterval(x);
    document.getElementById("demo").innerHTML = '<p class="helper">Or <a href="{{url('/resendotp')}}">Reset OTP</a> .</p>';
  }
}, 1000);
</script>



@endsection