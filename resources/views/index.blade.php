@extends('user.layout.app')

@section('content')

    <?php $banner_bg = asset('asset/img/banner-bg.jpg'); ?>
        <div class="banner row no-margin" style="background-image: url(asset/img/banner_new.jpg); height: 500px;     border-bottom: 5px solid #f10033; margin-top:91px;">
          
            <div class="container">
                <div class="col-md-8">
				<img src="../../../asset/img/200-200.png" style="width:341px;float: right;">
                    <h2 class="banner-head"><span class="strong">WELCOME TO BEST SERVICE PROVIDER </span><br>WE ARE HERE TO HELP YOU</h2>
                </div>
                <div class="col-md-4"> 
                    <div class="banner-form">
                    <div class="banner-form-w">
                        <div class="row no-margin fields">
                            <div class="left">
                                <img src="{{asset('asset/img/get-service-1.png')}}" style="border: 2px solid #fff; border-radius: 50%;">
                            </div>
                            <div class="right">
                                <a href="{{url('register')}}">
                                    <h3 style="color:#fff;">Get a @lang('main.service')</h3>
                                    <h5>SIGN UP <i class="fa fa-chevron-right"></i></h5>
                                </a>
                            </div>
                        </div>
                        <div class="row no-margin fields">
                            <div class="left">
                                <img src="{{asset('asset/img/provide-a-service.png')}}" style="border: 2px solid #fff; border-radius: 50%;">
                            </div>
                            <div class="right">
                                <a href="{{url('/provider/register')}}">
                                    <h3 style="color:#fff;">Provide a @lang('main.service')</h3>
                                    <h5>SIGN UP <i class="fa fa-chevron-right"></i></h5>
                                </a>
                            </div>
                        </div>

                         <p class="note-or" style="color:#fff;"><i class="fa fa-hand-o-right" aria-hidden="true" style="color: #fff; font-size: 18px;"></i> Or <a href="{{url('/provider/login')}}">sign in</a> with your @lang('main.provider') account.</p>
                        
                    </div>
                </div>
                </div>
            </div>
        </div>
		
		
		
		<section id="about" class="about">
			<div class="about-decor">
				<div class="about-circle1"><img src="asset/img/team1.png" alt="team1"></div>
					<div class="about-circle2"><img src="asset/img/main-banner1.png" alt="banner1"></div>
			</div>
			<div class="container">
				<div class="row ">
					<div class="col-md-5">
						<div class="about-contain">
                    <div>
                        <h2 class="title">Download the app to <span> enjoy the services</span></h2>
                       
					 <div class="col-sm-12 col-md-12">
                        <ul class="feature-style">
                            <li>
                                
                                
                                <div>
                                    <p>This is a chance SAVE up to 30% on your first booking.</p>
                                </div>
                            </li>
                            <li>
                               
                               
                                <div>
                                    <p>You can easily book a bike service or repair from your mobile phone in just 2 click via this App and  our website.</p>
                                </div>
                            </li>
                            <li>
                               
                             
                                <div>
                                    <p>This is the fastest service platform to serve our costumer in Delhi and NCR.</p>
                                </div>
                            </li>
							 
                        </ul>
                    </div>
					  <div class="row sm-mb">
                            <div class="col-sm-6">
                                <ul class="about-style">
                                    <li class="abt-hover">
                                        <div class="about-icon">
                                            <div class="icon-hover"><img src="asset/img/icon1.png" alt="easy-to-customized"></div>
                                        </div>
                                        <div class="about-text">
                                            <h3>Electrician </h3></div>
                                    </li>
                                    <li class="abt-hover">
                                        <div class="about-icon">
                                            <div class="icon-hover"><img src="asset/img/icon3.png" alt="easy-to-use"></div>
                                        </div>
                                        <div class="about-text">
                                            <h3>Plumbing </h3></div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-sm-6">
                                <ul class="about-style">
                                    <li class="abt-hover">
                                        <div class="about-icon">
                                            <div class="icon-hover"><img src="asset/img/icon2.png" alt="Awasome-Design"></div>
                                        </div>
                                        <div class="about-text">
                                            <h3>Carpenter </h3></div>
                                    </li>
                                    <li class="abt-hover">
                                        <div class="about-icon">
                                            <div class="icon-hover"><img src="asset/img/icon4.png" alt="SEO-Friendly"></div>
                                        </div>
                                        <div class="about-text">
                                            <h3>Mechanic </h3></div>
                                    </li>
                                </ul>
                            </div>
							
							<div class="col-md-6 text-right">
    
								<a href="https://play.google.com/store/apps/details?id=com.ksbm.BestServices">
									<img target="_blank" class="play-st" src="asset/img/playstore.png">
								</a>
							</div>
                        </div>
                      
                    </div>
                </div>
            </div>
            <div class="col-md-7 d-medium-none">
                <div class="about-right">
                    <div class="about-phone"><img src="asset/img/aboutus.png" class="img-fluid" alt="aboutus"></div>
                </div>
            </div>
        </div>
    </div>
</section>



<section id="feature" class="feature">
    <div class="feature-decor">
        <div class="feature-circle1"><img src="asset/img/feature2.png" alt=""></div>
    </div>
    <div class="container">
        <div class="row">
            <div class="feature-phone"><img src="asset/img/222.png" class="img-fluid" alt=""></div>
            <div class="offset-lg-4 col-lg-8">
                <div class="row">
                    <div class="col-sm-12 mrgn-md-top">
                        <h2 class="title">Download the app to become <span> a service Provider</span></h2></div>
                    <div class="col-sm-12 col-md-12">
                        <ul class="feature-style">
                            <li>
                                
                                
                                <div>
                                    <p>This is a chance to grow your business 200% in upcoming time.
 </p>
                                </div>
                            </li>
                            <li>
                               
                               
                                <div>
                                    <p>Your nearby customer can easily find you and book a service/Repair in few seconds via this App.
</p>
                                </div>
                            </li>
                            <li>
                               
                             
                                <div>
                                    <p>Online and cash payment facilities are also available in App.
 </p>
                                </div>
                            </li>
							  <li>
                               
                             
                                <div>
                                    <p>All past and upcoming service bookings details are available in your App history.

 </p>
                                </div>
                            </li>
							 <li>
                               
                             
                                <div>
                                    <p>You can transfer your fund on weekly basis.


 </p>
                                </div>
                            </li>
							
							<li>
                                <div>
                                    <p>Our team always available to resolve any type of App related problems 24x7.
 </p>
                                </div>
                            </li>
							<li>
                                <div>
                                    <p>This is the chance to join young generation to increase your business.
 </p>
                                </div>
                            </li>
							<li>
                                <div>
                                    <p>Time to time training facility are available for your personality development.  
 </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    
                </div>
				<div class="col-md-6 text-right pad-po pull-right">
    
								<a href="https://play.google.com/store/apps/details?id=com.ksbm.BestServicesProviders">
									<img target="_blank" class="play-st1" src="asset/img/playstore.png">
								</a>
							</div>
            </div>
        </div>
    </div>
</section>

@endsection
