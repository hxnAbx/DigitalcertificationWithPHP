<?php

if(isset($_POST['method'])){


    ini_set("soap.wsdl_cache_enabled","0");
    $endpoint = "?wsdl"
    $local_cert = "localCert.pem"
    $method = $_POST['method'];
    $option  = array(
        'location'    => $endpoint, 
        'keep_alive'  => true, 
        'trace'       => 1,
        "exceptions"    => false,
        'local_cert'  => $local_cert,
        'cache_wsdl'  => WSDL_CACHE_MEMORY,
        //for ByPASSING SSL you need to add StreamContext
            "stream_context" => stream_context_create(
                array(
                    'ssl' => array(
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    )
                )
            )
         );


    switch ($method) {
        case 'sendOTP':
             
             $client = new SoapClient(  $endpoint ,$option  );
             $prams = array( "cell" => $phoneNumber );
             $result = $client->__soapCall('sendOTP',array($prams));
             var_dump($result)
             // $optpCode = $result->sendOTPReturn;

            break;

            case 'doKYC':
                
            $transId    = $_POST['tranID'];
            $fname      = $_POST['fname'];
            $lname      = $_POST['lname'];
            $cnic       = $_POST['cnic'];
            $cellNo     = $_POST['cellno'];
            $bank_name  = $_POST['bank_name'];
            $iban       = $_POST['iban'];
            $client = new SoapClient(  $endpoint ,$opt  );
            $prams = array( "trans_id" => $transId , "fname" => $fname , "lname" =>  $lname , "cnic" => $cnic ,   "cell_no" => $cellNo  , "bank_name" => $bank_name, "iban" =>  $iban );
            $result = $client->__soapCall('doKYC',array($prams));
            var_dump($result);
                break;

            case 'getKYCStatus':
                
                $transId    = $_POST['tranID'];
                $cnic       = $_POST['cnic'];
                $client = new SoapClient(  $endpoint ,$opt  );
                $prams = array( "trans_id" => $transId , "cnic" => $cnic);
                $result = $client->__soapCall('getKYCStatus',array($prams));
                 var_dump($result);
                //$responseStatus = $result->getKYCStatusReturn;
                break;

            case 'verifyBiometric':
            $transId        = $_POST['tranID'];
            $ses_id         = $_POST['ses_id'];
            $tempId         = $_POST['tempId'];
            $template       = $_POST['template'];
            $fig_index      = $_POST['fig_index'];
            $client = new SoapClient(  $endpoint ,$opt  );
            $prams = array( "trans_id" => $transId , "session_id" => $ses_id, "template_type" => $tempId ,"template" => $template, 'finger_index' => $fig_index );
            try{
            $result = $client->__soapCall('verifyBiometric',array($prams));
            var_dump($result);
            }
             catch(Exception $e) {
                var_dump($e);
             }
            //$response = $result->verifyBiometricReturn;
                break;

            case 'spkacRequest':
            $username = $_POST['username'];
            $passwrod = $_POST['password'];
            $spkac = $_POST['spkac'];
            $hardTokenSN = $_POST['hardTokenSN'];
            $responseType = $_POST['responseType'];
           try {
              $client = new SoapClient( $endpoint,  $opt );
               $prams = array( 
                'arg0'  => $username ,
                 'arg1'=> $passwrod,
                 'arg2' => $spkac,
                 'arg3' =>  $hardTokenSN ,
                 'arg4' =>$responseType, 
                 );
                $result = $client->__soapCall('spkacRequest',array($prams));
                  var_dump($result);
           }
           catch(Exception $e){
            var_dump( $e->getMessage() );
           }
            break;

            case 'pkcs10Request':
              $username = $_POST['username'];
              $passwrod = "*********";
              $pkcs10 = $_POST['pkcs10'];
              $hardTokenSN = $_POST['hardTokenSN'];
              $responseType = $_POST['responseType'];
              try{
                 $prams = array( 
                    'arg0'  => $username ,
                     'arg1'=> $passwrod,
                     'arg2' => $spkac,
                     'arg3' =>  $hardTokenSN ,
                     'arg4' =>$responseType, 
                     );
                $client = new SoapClient( $endpoint,  $opt );
                 $result = $client->__soapCall('pkcs10Request',array($prams));
              }
              catch(Exception $e){
                var_dump($e);
              }
               break;
        default:
           echo "Not a valid method is posted";
            break;
    }


}
else {
    echo "Method is not Posted";
}

?>
