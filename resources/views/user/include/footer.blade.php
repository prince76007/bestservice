<style>
	.footer .app img{
		border: 2px solid #b36776;
		padding: 2px;
	}
</style>
    <div class="row footer no-margin">
        <div class="container">
            <div class="col-md-6 text-left">
                <p>&copy;{{date('Y')}} {{Setting::get('site_title','Tranxit')}}</p>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{Setting::get('app_store_link','#')}}" class="app"><img src="{{asset('asset/img/appstore.png')}}"></a>
                <a href="https://play.google.com/store/apps/details?id=com.ksbm.BestServices" target="_blank" class="app"><img src="{{asset('asset/img/playstore.png')}}"></a>
            </div>
        </div>
    </div>