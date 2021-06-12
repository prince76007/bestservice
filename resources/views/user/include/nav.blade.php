
<style>
	.dash-left .user-img{
		    background: #fe1743;
	}
	
	.user-img h4 {
    margin-bottom: 0;
    color: #fff;
}

.navbar{
	
	background:#000 !important;
}
</style>
<div class="col-md-3">
    <div class="dash-left">
        <div class="user-img">
            <?php $profile_image = img('app/public/'.Auth::user()->picture); ?>
            <div class="pro-img" style="background-image: url({{$profile_image}});"></div>
            <h4>{{Auth::user()->first_name}} {{Auth::user()->last_name}}</h4>
        </div>
        <div class="side-menu">
             <ul>
                <li><a href="{{url('dashboard')}}"><i class="fa fa-angle-double-right" aria-hidden="true"></i> @lang('user.dashboard')</a></li>
                <li><a href="{{url('my_request')}}"><i class="fa fa-angle-double-right" aria-hidden="true"></i> @lang('user.my_trips')</a></li>
                <li><a href="{{url('profile')}}"><i class="fa fa-angle-double-right" aria-hidden="true"></i> @lang('user.profile.profile')</a></li>
                <li><a href="{{url('change/password')}}"><i class="fa fa-angle-double-right" aria-hidden="true"></i> @lang('user.profile.change_password')</a></li>
                <!--li><a href="{{url('/payment')}}"><i class="fa fa-angle-double-right" aria-hidden="true"></i> @lang('user.payment')</a></li-->
                <li><a href="{{url('/promotion')}}"><i class="fa fa-angle-double-right" aria-hidden="true"></i> @lang('user.promotion')</a></li>
                <li><a href="{{url('upcoming/trips')}}"><i class="fa fa-angle-double-right" aria-hidden="true"></i> @lang('user.upcoming_trips')</a></li>
                <li style="background:#efe9e9;"><a href="{{url('/wallet')}}"><i class="fa fa-angle-double-right" aria-hidden="true"></i> @lang('user.my_wallet') <span class="pull-right">{{currency(Auth::user()->wallet_balance)}}</span></a></li>
                  <li><a href="{{url('help')}}"><i class="fa fa-angle-double-right" aria-hidden="true"></i> @lang('user.help')</a></li>
                <li><a href="{{ url('/logout') }}"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"><i class="fa fa-sign-out" aria-hidden="true"></i> @lang('user.profile.logout')</a></li>
                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
            </ul>
        </div>
    </div>
</div>