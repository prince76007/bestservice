@extends('user.layout.base')

@section('title', 'Wallet ')

@section('content')

<?php




?>


<div class="col-md-9">
    <div class="dash-content">
        <div class="row no-margin">
            <div class="col-md-12">
                <h4 class="page-title">@lang('user.my_wallet')</h4>
            </div>
        </div>
        @include('common.notify')

        <div class="row no-margin">
            <div class="col-md-6">
                     
                    <div class="wallet">
                        <h4 class="amount">
                            <span class="price">{{currency(Auth::user()->wallet_balance)}}</span>
                            <span class="txt">@lang('user.in_your_wallet')</span>
                        </h4>
                    </div>                                                               

                </div>
            <form action="{{ url('loadwallet') }}" name="payuForm" method="POST" id="myfrm">
           
                
                

                <div class="col-md-6">
                    <h6><strong>@lang('user.add_money')</strong></h6>

                    <input type="number" class="form-control" name="amount" placeholder="Enter Amount" >

                    <div class="input-group full-input">
                        <input type="hidden" class="form-control" name="user_id" value="{{(Auth::user()->id)}}">
                    </div>
                    <br>
                    
                       
                    
                    
                    <!-- <button type="submit" class="full-primary-btn fare-btn">@lang('user.add_money')</button>  -->

                   


          <button type="submit" className="full-primary-btn fare-btn"> PAY ONLINE</button> 
                       
                    

                </div>
              
            </form>
        </div>

        <button id="frm" class="full-primary-btn fare-btn">@lang('user.add_money')</button>

    </div>
    <script
  src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script type="text/javascript">
    $('#myfrm').hide();


    $('#frm').click(function(){
        $('#myfrm').show();
        $('#frm').hide();
    });

   
</script>
</div>





@endsection
