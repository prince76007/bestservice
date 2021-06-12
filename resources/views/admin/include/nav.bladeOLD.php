<div class="site-sidebar">
	<div class="custom-scroll custom-scroll-light">
		<ul class="sidebar-menu">
			<li class="menu-title">Main</li>
			<li>
				<a href="{{route('admin.dashboard')}}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-anchor"></i></span>
					<span class="s-text">Dashboard</span>
				</a>
			</li>
			
			<li class="menu-title">Members</li>
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-crown"></i></span>
					<span class="s-text">@lang('main.user')s</span>
				</a>
				<ul>
					<li><a href="{{route('admin.user.index')}}">List @lang('main.user')s</a></li>
					<li><a href="{{route('admin.user.create')}}">Add New @lang('main.user')</a></li>
				</ul>
			</li>
			
			
		
			
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-car"></i></span>
					<span class="s-text">@lang('main.provider')s</span>
				</a>
				<ul>
					<li><a href="{{route('admin.provider.index')}}">List @lang('main.provider')s</a></li>
					<li><a href="{{route('admin.provider.create')}}">Add New @lang('main.provider')</a></li>
				</ul>
			</li>
			
			
			
			
<li class="menu-title">Slider</li>
<li class="with-sub">
	<a href="#" class="waves-effect  waves-light">
		<span class="s-caret"><i class="fa fa-angle-down"></i></span>
		<span class="s-icon"><i class="ti-crown"></i></span>
		<span class="s-text">Slider </span>
	</a>
	<ul>
		<li><a href="{{ URL::to("add_slider")}}">Add Slider</a></li>
		<li><a href="{{ URL::to("all_slider")}}">All User Slider </a></li>
		<li><a href="{{ URL::to("all_slider_as_provider")}}">All Provider Slider </a></li>
	</ul>
</li>				
			
			
			
			<li class="menu-title">Details</li>
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-map-alt"></i></span>
					<span class="s-text">Map</span>
				</a>
				<ul>
					<li><a href="{{route('admin.user.map')}}">@lang('main.user') Locations</a></li>
					<li><a href="{{route('admin.provider.map')}}">@lang('main.provider') Locations</a></li>
				</ul>
			</li>
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-view-grid"></i></span>
					<span class="s-text">Ratings & Reviews</span>
				</a>
				<ul>
					<li><a href="{{route('admin.user.review')}}">@lang('main.user') Ratings</a></li>
					<li><a href="{{route('admin.provider.review')}}">@lang('main.provider') Ratings</a></li>
				</ul>
			</li>
			<li class="menu-title">@lang('main.service')s</li>
			<li>
				<a href="{{route('admin.request.history')}}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-infinite"></i></span>
					<span class="s-text">@lang('main.service')s History</span>
				</a>
			</li>
			<li>
				<a href="{{route('admin.scheduled.request')}}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-palette"></i></span>
					<span class="s-text">Scheduled Services</span>
				</a>
			</li>
			<li class="menu-title">General</li>
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-view-grid"></i></span>
					<span class="s-text">Service Types</span>
				</a>
				<ul>
					<li><a href="{{route('admin.service.index')}}">List Service Types</a></li>
					<li><a href="{{route('admin.service.create')}}">Add New Service Type</a></li>
				</ul>
			</li>
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-layout-tab"></i></span>
					<span class="s-text">Documents</span>
				</a>
				<ul>
					<li><a href="{{route('admin.document.index')}}">List Documents</a></li>
					<li><a href="{{route('admin.document.create')}}">Add New Document</a></li>
				</ul>
			</li>

			<li class="menu-title">Accounts</li>
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-crown"></i></span>
					<span class="s-text">Statements</span>
				</a>
				<ul>
					<li><a href="{{ route('admin.ride.statement') }}">Overall @lang('main.service') Statements</a></li>
					<li><a href="{{ route('admin.ride.statement.provider') }}">@lang('main.provider') Statement</a></li>
					<li><a href="{{ route('admin.ride.statement.today') }}">Daily Statement</a></li>
					<li><a href="{{ route('admin.ride.statement.monthly') }}">Monthly Statement</a></li>
					<li><a href="{{ route('admin.ride.statement.yearly') }}">Yearly Statement</a></li>
				</ul>
			</li>
			
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-layout-tab"></i></span>
					<span class="s-text">Promocodes</span>
				</a>
				<ul>
					<li><a href="{{route('admin.promocode.index')}}">List Promocodes</a></li>
					<li><a href="{{route('admin.promocode.create')}}">Add New Promocode</a></li>
				</ul>
			</li>
			
			<li class="menu-title">Payment Details</li>
			<li>
				<a href="{{route('admin.payment')}}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-infinite"></i></span>
					<span class="s-text">Payment History</span>
				</a>
			</li>
			<li>
				<a href="{{route('admin.payment.setting')}}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-money"></i></span>
					<span class="s-text">Payment Settings</span>
				</a>
			</li>
			<li class="menu-title">Settings</li>
			<li>
				<a href="{{route('admin.setting')}}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-settings"></i></span>
					<span class="s-text">Site Settings</span>
				</a>
			</li>
			
			<li class="menu-title">Others</li>
			<li>
				<a href="{{route('admin.help')}}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-help"></i></span>
					<span class="s-text">Help</span>
				</a>
			</li>
			<li>
				<a href="{{route('admin.translation')}}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-smallcap"></i></span>
					<span class="s-text">Translations</span>
				</a>
			</li>
			<li class="menu-title">Account</li>
			<li>
				<a href="{{route('admin.profile')}}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-user"></i></span>
					<span class="s-text">Account Settings</span>
				</a>
			</li>
			<li>
				<a href="{{route('admin.password')}}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-exchange-vertical"></i></span>
					<span class="s-text">Change Password</span>
				</a>
			</li>
			<li class="compact-hide">
				<a href="{{ url('/admin/logout') }}"
                            onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
					<span class="s-icon"><i class="ti-power-off"></i></span>
					<span class="s-text">Logout</span>
                </a>

                <form id="logout-form" action="{{ url('/admin/logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
			</li>
			
		</ul>
	</div>
</div>