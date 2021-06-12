@extends('provider.layout.auth')

@section('content')
<div class="col-md-12">
    <a class="log-blk-btn" href="{{ url('/provider/register') }}">CREATE NEW ACCOUNT </a>
    <h3>Sign IIn</h3>
</div>

<div class="col-md-12">
    <form role="form" method="POST" action="{{ url('/provider/login') }}">
        {{ csrf_field() }}

        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email" autofocus>

        @if ($errors->has('email'))
            <span class="help-block">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif

        <input id="password" type="password" class="form-control" name="password" placeholder="Password">

        @if ($errors->has('password'))
            <span class="help-block">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
        @endif

        <div class="checkbox">           
            <input type="checkbox" name="remember"><span> Remember Me</span>
            
        </div>


        <button type="submit" class="log-teal-btn">
            Login
        </button>

							<!--br />
                        <p style="margin-left:265px">OR</p>
                        <br />
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                              <a href="{{url('redirect3')}}" class="btn btn-primary">Login with Facebook</a>
                            </div>
                        </div>-->
						
						
                        <p style="margin-left:265px">OR</p>
                        <br />
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                              <a href="{{url('/redirect?type=2')}}" class="btn btn-primary">Login with Google</a>
                            </div>
                        </div>

			 <br /></br>
                        <!--div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                              <a href="{{url('/redirect3?type=2')}}" class="btn btn-primary">Login with Facebook </a>
                            </div>

                        </div-->

        <p class="helper"><a href="{{ url('/provider/password/reset') }}">Forgot Your Password?</a></p>   
    </form>
</div>
@endsection
