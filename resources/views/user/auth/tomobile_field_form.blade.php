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
                        <h3> Plz Submit Mobile </h3>
                    </div>
                    <form role="form" method="POST" action="{{ url('/mobilenumbersubmited') }}">
                        {{ csrf_field() }}

                   

                        <div class="col-md-12">
                            <input type="text" autofocus class="form-control" placeholder="Email" name="email" value="{{ $email }}" readonly="">

                        </div>


                        <div class="col-md-12">
                            <input type="text" autofocus class="form-control" placeholder="Enter Mobile" name="mobile" value="">

                            @if($errors->has('mobile'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('mobile') }}</strong>
                                </span>
                            @endif
                        </div>
                        
                        <div class="col-md-12">
                                <button class="log-teal-btn" type="submit">Submit Mobile Number</button>
                        </div>
                    </form>     

                    <div class="col-md-12">
                        <p id="demo"></p>
                          <p class="helper"><a href="{{url('/logout')}}">Not MY email</a> </p>
                    </div>

                </div>


                <div class="log-copy"><p class="no-margin">&copy;{{date('Y')}} {{Setting::get('site_title','Tranxit')}}</p></div>
                </div>
            </div>
        </div>
    </div>



@endsection