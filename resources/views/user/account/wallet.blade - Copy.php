@extends('user.layout.base')

@section('title', 'Wallet ')

@section('content')

<?php


    $MERCHANT_KEY = "oevZCuqF"; // add your id
    $SALT = "uxptDjtOjI"; // add your id

  // $PAYU_BASE_URL = "https://sandboxsecure.payu.in";
   $PAYU_BASE_URL = "https://secure.payu.in";
    $action = '';
    $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    $posted = array();
    $amount=100;
    $posted = array(
        'key' => $MERCHANT_KEY,
        'txnid' => $txnid,
        'amount' =>$amount,
        'firstname' => Auth::user()->first_name,
        'email' => Auth::user()->email,
        'productinfo' => 'Payment for ',
        'surl' => url('payment-response'),
        'furl' =>url('payment-cancel'),
        'service_provider' => 'payu_paisa',
    );

    if(empty($posted['txnid'])) {
        $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    } 
    else 
    {
        $txnid = $posted['txnid'];
    }

    $hash = '';
    $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
    
    if(empty($posted['hash']) && sizeof($posted) > 0) {
        $hashVarsSeq = explode('|', $hashSequence);
        $hash_string = '';  
        foreach($hashVarsSeq as $hash_var) {
            $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
            $hash_string .= '|';
        }
        $hash_string .= $SALT;

        $hash = strtolower(hash('sha512', $hash_string));
        $action = $PAYU_BASE_URL . '/_payment';
    } 
    elseif(!empty($posted['hash'])) 
    {
        $hash = $posted['hash'];
        $action = $PAYU_BASE_URL . '/_payment';
    }


$name=Auth::user()->first_name;
$email=Auth::user()->email;

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
            <form action="{{ $action }}" name="payuForm" method="POST" id="myfrm">
           
                
                

                <div class="col-md-6">
                    <h6><strong>@lang('user.add_money')</strong></h6>

                    <input type="number" class="form-control" name="amount" placeholder="Enter Amount" >

                    <div class="input-group full-input">
                        <input type="hidden" class="form-control" name="user_id" value="{{(Auth::user()->id)}}">
                    </div>
                    <br>
                    
                       
                    
                    
                    <!-- <button type="submit" class="full-primary-btn fare-btn">@lang('user.add_money')</button>  -->

                   


            <input type="hidden" name="key" value="{{ $MERCHANT_KEY }}" /><br />
                             
            <input type="hidden" name="hash" value="{{ $hash }}"/>
            <input type="hidden" name="txnid" value="{{ $txnid }}" />
            <input type="hidden" name="amount" value="{{ $amount }}" />
            <input type="hidden" name="firstname" id="firstname" value="{{ $name }}" /> 
            <input type="hidden" name="email" id="email" value="{{ $email }}" />
            <input type="hidden" name="productinfo" value="Payment for add wallet money" />
            <input type="hidden" name="surl" value="{{ url('payment-response')}}" />
            <input type="hidden" name="furl" value="{{ url('payment-cancel')}}" />
           <input type="hidden" name="service_provider" value="payu_paisa"  />
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



<script>
    /*var hash = '<?php echo $hash ?>';
    function submitPayuForm() {
      if(hash == '') {
        return;
      }
      var payuForm = document.forms.payuForm;
           payuForm.submit();
    }*/
  </script>

@endsection
