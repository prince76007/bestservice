<nav class="navbar navbar-inverse navbar-fixed-top" id="sidebar-wrapper" role="navigation">
    <ul class="nav sidebar-nav">
        <li>
            <a href="{{ route('provider.earnings') }}">Partner Earnings</a>
        </li>
         <li>
            <a href="{{ route('provider.upcoming') }}">Upcoming Services</a>
        </li>
        <li>
            <a href="{{ route('provider.profile.index') }}">Profile</a>
        </li>

        <li>
            <a href="{{ url('/provider/logout') }}"
                onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
                Logout
            </a>
        </li>
    </ul>
</nav>