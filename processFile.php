<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '1535K');
ini_set("soap.wsdl_cache_enabled","0");

define('TOKEN_TYPE_P12', 'P12');
define('STATUS_NEW', 10);
define('REVOKATION_REASON_UNSPECIFIED', 0);
define('MATCH_TYPE_EQUALS',0);
define('MATCH_WITH_USERNAME', 0);

define('CERT', 'localCert.pem');
$url ="ejbcaws/ejbcaws?WSDL";
$endpoint = 'ejbcaws/ejbcaws';
$passphrase='********';
class userDataVOWS

{
    public $username;
    public $password;
    public $clearPwd;
    public $subjectDN;
    public $caName;
    public $email;
    public $status;
    public $tokenType;
    public $endEntityProfileName;
    public $certificateProfileName;
    public $certificateSerialNumber;
    public $cardNumber;
    public $endTime;
    public $extendedInformation;
    public $hardTokenIssuerName;
    public $keyRecoverable;
    public $sendNotification;
    public $startTime;
    public $subjectAltName;
}
class userMatch
{
    public $matchtype;
    public $matchvalue;
    public $matchwith;
}

class extendedInformationWS
{
    public $name;
    public $value;
}

$classmap = array(
    'userDataVows' => 'userDataVows',
    'userMatch' => 'userMatch',
    'extendedInformationWS' => 'extendedInformationWS'
);

class WrappedSoapClient extends SoapClient {
  protected function callCurl($url, $data, $action) {
     $handle   = curl_init($url);
     curl_setopt($handle, CURLOPT_URL, $url);
     curl_setopt($handle, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml", 'SOAPAction: "' . $action . '"'));
     curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
     curl_setopt($handle, CURLOPT_SSLVERSION, 3);
     curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, FALSE);
     curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE);
     curl_setopt($handle, CURLOPT_SSLCERT, CERT);
     $response = curl_exec($handle);
     if (empty($response)) {
       throw new SoapFault('CURL error: '.curl_error($handle),curl_errno($handle));
     }
     curl_close($handle);
     return $response;
   }

   public function __doRequest($request,$location,$action,$version,$one_way = 0) {
       return $this->callCurl($location, $request, $action);
   }
 }

   $client=new SoapClient( $url, array( 'trace' => 1, 'local_cert' => CERT,  'location'    => $endpoint, 'passphrase' => $passphrase, 'classmap' => $classmap));


function user($name,$cnic,$email) {
    $extendedInformation = new extendedInformationWS();
    $extendedInformation->name=" ";
    $extendedInformation->value="";
    $userData=new userDataVOWS();
    $userData->caName = "";
    //$userData->caName = "ManagementCA";
    /* $userData->cardNumber=null; */
    $userData->certificateProfileName = "";
    /* $userData->certificateSerialNumber = NULL; */
    $userData->clearPwd = FALSE;
    $userData->email = $email;
    $userData->endEntityProfileName = "";
    /* $userData->endTime=null; */
    $userData->extendedInformation=$extendedInformation;
    /* $userData->hardTokenIssuerName=null; */
    $userData->keyRecoverable=FALSE;
    $userData->password = "";
    $userData->sendNotification=FALSE;
    /* $userData->startTime=null; */
    $userData->status = STATUS_NEW;
    $userData->subjectAltName="";
   //$userData->subjectDN = "CN=TEST,O=LA POSTE,C=FR";
    $userData->subjectDN = "CN=".$name.",O=,OU=CNIC-".$cnic.",OU=UID-FSL-12346";
    //userData.setSubjectDN("CN=".concat("Hasan Abbas")+","+"O=".concat("Fortune Securities Limited")+","+"OU=".concat("CNIC - 4220112345678")+","+"OU=".concat("UID - FSL-12347"));
                        
    $userData->tokenType = "USERGENERATED";
    $userData->username = $name;
    return $userData;

}



function editUser($userData, $client)
{
    try {
        return $client->editUser(array('arg0' => $userData));
    } catch(Exception $e) {
        var_dump($e);
         return false;
       
    }
}

function generateCert($username, $client) {
    try {
        return $client->pkcs12Req(array(
            'arg0' => $username,
            'arg1' => "******",
            'arg2' => '',
            'arg3' => '',
            'arg4' => 'CERTIFICATE'));
    } catch (Exception $e) {
        var_dump($e);
         return false;
    }
}



function find_user($username, $client) {
    try{
        $matcher= new userMatch();
        $matcher->matchtype= MATCH_TYPE_EQUALS;
        $matcher->matchvalue = $username;
        $matcher->matchwith = MATCH_WITH_USERNAME;
        return $client->findUser(array("arg0" => $matcher));
         var_dump($client->__getLastResponse());
    } catch(Exception $e) {
        var_dump($e);
        return false;
    }

}



if( isset($_POST['method'])){
     $method = $_POST['method'];

     switch ( $method) {
         case 'editUser':
            $username = $_POST['username'];
            $email = $_POST['email'];
            $cnic = $_POST['cnic'];
            $result = editUser(user($username,$cnic,$email),$client);
             if($result)
                var_dump($result);
             break;

            case 'find_user':
            $username = $_POST['username'];
            $result = find_user($username , $client);
            if($result)
                var_dump($result);
             break;

             case 'generateCert':
             $username = $_POST['username'];
             $result = generateCert($username, $client);
             var_dump($result);
         default:
             # code...
          echo "Please post a relevant method";
             break;
     }
}
 
}else{
    echo "No method is posted";
}

?>
