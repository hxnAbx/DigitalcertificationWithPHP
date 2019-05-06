<?php
/* This will print all method present in WSDL */
error_reporting(E_ALL);
ini_set("soap.wsdl_cache_enabled","0");
try {
    error_reporting(E_STRICT | E_ALL);
    $endpoint = 'ejbcaws/ejbcaws';
    $local_cert = 'localCert.pem';
    $passphrase = '*****';
    $opt = array(
      "local_cert"  => $local_cert,
      'keep_alive'  => true,
      'location'    => $endpoint,
      'cache_wsdl'  => WSDL_CACHE_MEMORY,
      'exceptions'  => 1
    );
    $client = new SoapClient(
        'ejbcaws/ejbcaws?WSDL',   $opt
    );
	$res = $client->__getFunctions();
    var_dump($res);
}
catch(Exception $e) {
  var_dump( $e->getMessage() );
}
?>
