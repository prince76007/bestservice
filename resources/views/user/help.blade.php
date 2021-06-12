@extends('user.layout.base')

@section('title', 'Promotion')

@section('content')

<div class="col-md-9">
    <div class="dash-content">
        <div class="row no-margin">
            <div class="col-md-12">
                <h4 class="page-title">@lang('user.help')</h4>
            </div>
        </div>
        @include('common.notify')
        <div class="row no-margin payment">
            <div class="col-md-12">
                <h5 class="btm-border"><center>www.bestservicepoint.com</center></h5>
              
                    <div class="pay-option">
                        <center>
                            <img src="{{asset('asset/img/help.png')}}"> 
                            <h5 ><center>
                         <a class="btn btn-warning" href="callto:8178754492" style="background:#fe1743"><i style="color:#fff" class="fa fa-phone"></i></a> &nbsp;&nbsp;&nbsp;
                            <a class="btn btn-warning" href="mailto:info@bestservicepoint.com" style="background:#fe1743"><i style="color:#fff" class="fa fa-envelope"></i></a> &nbsp;&nbsp;&nbsp;
                            <a class="btn btn-warning" href="http://www.bestservicepoint.com/" style="background:#fe1743; " ><i style="color:#fff" class="fa fa-globe"></i></a> 
                                </center></h5>
                        </center>
                    </div>
                   
            </div>
        </div>

    </div>
</div>



@endsection