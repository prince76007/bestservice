<?php


    $MERCHANT_KEY = "oevZCuqF"; // add your id
    $SALT = "uxptDjtOjI"; // add your id

//   $PAYU_BASE_URL = "https://sandboxsecure.payu.in";
    $PAYU_BASE_URL = "https://secure.payu.in";
    $action = '';
    $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    $posted = array();

$furl=  url('load-wallet-payment-cancel') ;
$surl=url('load-wallet-payment-response');
    $posted = array(
        'key' => $MERCHANT_KEY,
        'txnid' => $txnid,
        'amount' =>$amount,
        'firstname' => Auth::user()->first_name,
        'email' => Auth::user()->email,
        'productinfo' => 'Payment for add wallet money',
        'surl' => $furl,
        'furl' =>$surl,
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
<html>
  <head>
  <script>
    var hash = '<?php echo $hash ?>';
    function submitPayuForm() {
      if(hash == '') {
        return;
      }
      var payuForm = document.forms.payuForm;
           payuForm.submit();
    }
  </script>
  </head>
  <body onload="submitPayuForm()">
    Processing.....
        <form action="<?php echo $action; ?>" method="post" name="payuForm"><br />
        

          <input type="hidden" name="key" value="{{ $MERCHANT_KEY }}" /><br />
                              </dd>
            <dd> <input type="hidden" name="hash" value="{{ $hash }}"/></dd>
           <dd> <input type="hidden" name="txnid" value="{{ $txnid }}" /></dd>
           <dd> <input type="hidden" name="amount" value="{{ $amount }}" /></dd>
           <dd> <input type="hidden" name="firstname" id="firstname" value="{{ $name }}" /> </dd>
           <dd> <input type="hidden" name="email" id="email" value="{{ $email }}" /></dd>
          <dd>  <input type="hidden" name="productinfo" value="Payment for add wallet money" /></dd>
         <dd>   <input type="hidden" name="surl" value="{{ $surl }}" /></dd>
          <dd>  <input type="hidden" name="furl" value="{{ $furl }}" /></dd>
         <dd>    <input type="hidden" name="service_provider" value="payu_paisa"  /></dd>

            <?php
            if(!$hash) { ?>
                <input type="submit" value="Submit" />
            <?php } ?>
        </form>
  </body>
</html>
