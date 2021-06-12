<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

   
	<title>Book Online Bike Repair Service in Delhi NCR, Noida</title>
	

    <meta name="description" content="BestService offers online bike servicing, repairing, Breakdown to our customers round the clock in Delhi NCR, Noida, Faridabad, Ghaziabad and Gurgaon">
	<meta name="keywords" content="bike service in Delhi NCR, bike service in Faridabad, bike service in Ghaziabad, bike service in Gurgaon.  ">
    <meta name="google-site-verification" content="2sEufMMwgMKcVfyQ8OjrnjXuwcEqU5NJQhtSqFgaTmk" />
	
	
    <link rel="shortcut icon" type="image/png" href="{{ Setting::get('site_icon') }}"/>

    <link href="{{asset('asset/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('asset/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('asset/css/style.css')}}" rel="stylesheet">le
	
	<style>
		.navbar-right i{
			background: #f10033;
    color: #fff;
    width: 40px;
    height: 40px;
    line-height: 40px;
    text-align: center;
    margin-right: 0;
    border-radius: 50%;
			
		}
		
	
		.navbar-nav>li>a {
    font-size: 16px;
    padding: 20px 11px !important;
		/* border-top: 4px solid #333;}
		
	</style>r
</head>

<body>

    <div id="wrapper">
        <div class="overlay" id="overlayer" data-toggle="offcanvas"></div>


             <!-- Sidebar -->
            <nav class="navbar navbar-inverse navbar-fixed-top" id="sidebar-wrapper" role="navigation">
                <ul class="nav sidebar-nav">
                    <li>                   
                    </li>
                    <li class="full-white">
                        <a href="{{ url('/register') }}">GET A SERVICE</a>
                    </li>
                    <li class="white-border">
                        <a href="{{ url('/provider/login') }}">BECOME A @lang('main.provider')</a>
                    </li>
                    <?php use App\Page; 
					$data=Page::get();
					foreach($data as $row){
					?>
				
					<li><a href="{{ url('view_pages/'.$row->id) }}">{{ $row->title}}</a></li>
					
					
				<?php } ?>

					 <ul class="nav navbar-nav navbar-right">
					<li style="float:left; width:20%;"><a href="https://www.facebook.com/bestservicepoint" ><i class="fa fa-facebook"></i></a></li>
					<li style="float:left; width:20%;"><a href="https://twitter.com/bestservicepoin" target="_blank" ><i class="fa fa-twitter"></i></a></li>
					
					<li style="float:left; width:20%;"><a href="#" ><i class="fa fa-instagram"></i></a></li>
									   <li style="float:left; width:20%;"><a href="#" ><i class="fa fa-youtube-play"></i></a></li>

                </ul>
            </nav>
            <!-- /#sidebar-wrapper -->

            <div id="page-content-wrapper">

            <header>
                <nav class="navbar navbar-fixed-top">
                  <div class="container-fluid">
                    <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                      </button>

                      <button type="button" class="hamburger is-closed" data-toggle="offcanvas">
                        <span class="hamb-top"></span>
                        <span class="hamb-middle"></span>
                        <span class="hamb-bottom"></span>
                    </button>

                      <a class="navbar-brand" href="{{url('/')}}">Best Services</a>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    
                    <ul class="nav navbar-nav navbar-right">
					<li><a href="https://www.facebook.com/bestservicepoint" target="_blank"><i class="fa fa-facebook"></i></a></li>
					
					<li><a href="https://twitter.com/bestservicepoin" target="_blank"><i class="fa fa-twitter"></i></a></li>
					
					<li><a href="https://www.instagram.com/best_service_point/" target="_blank"><i class="fa fa-instagram"></i></a></li>
					
					<li><a href="https://www.youtube.com/channel/UCbY23pY6-x_jCdZDAA4rzrQ/about" target="_blank"><i class="fa fa-youtube-play"></i></a></li>
									   
                      <li><a class="sing1" href="{{url('/login')}}">Signin</a></li>
                      <li><a class="menu-btn" href="{{url('/provider/login')}}">Become a @lang('main.provider')</a></li>
					  
					  
                    </ul>
                    </div>
                  </div>
                </nav>
            </header>

            @yield('content')
                <div class="page-content no-margin">

					<div class="footer_area">
						<div class="container">
							<div class="row">
								<div class="col-md-3">
									
								</div>
								<div class="col-md-5">
									<ul class="nav nav-pills">
                                    <?php
					$data=Page::get();
					foreach($data as $row){
					?>
				
					<li><a href="{{ url('view_pages/'.$row->id) }}">{{ $row->title}}</a></li>
				<?php } ?>
									  
										
									</ul>
								</div>
								<div class="col-md-4">
								<p style="margin-top:10px;"><strong style="font-size: 16px;">Address:-</strong>Office 488/6 2nd Floor, Near Radha Krishna Mandir, above Muthoot Finance, A-Block, Dilshad Garden, Delhi, 110095</p>
								</div>
							</div>
						<div>
					</div>
						</div>
						</div>
                    <div class="footer home-footer row no-margin no-padding">
                        
                       
                            
                        <div class="row no-margin">
                                <div class="col-md-12 copy no-margin">
                                    <!--  <p>Copyrights {{date('Y')}} {{Setting::get('site_title','Tranxit')}}.</p> */ -->
                                    <p>Powered By Ride'x Corporations Developed & Maintained by KSBM INFOTECH</p>
                                </div>
                        </div>

                    </div>
                </div>
            </div>


    </div>

    <script src="{{asset('asset/js/jquery.min.js')}}"></script>
    <script src="{{asset('asset/js/jquery.min.js')}}"></script>
    <script src="{{asset('asset/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('asset/js/scripts.js')}}"></script>
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-SBWFJR9XJF"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'G-SBWFJR9XJF');
	</script>
</body>
</html>
