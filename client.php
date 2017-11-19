<head>
  <title>VAN OPERATOR</title>
  <h1>VAN Infomation</h1>
<form method="post">
<input type="submit" name="Operator" value="Operator" />
<input type="submit" name="Routes" value="Routes"  />
<input type="submit" name="Timetable" value="Timetable" />
</form>
</head>
<?php
require_once "nusoap.php";
$client = new nusoap_client("http://localhost:8080/www/soap/server.php?wsdl");
$error = $client->getError();
		if ($error) {
		    echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
		}

		//Use basic authentication method
		//Encryption:
$textToEncrypt = "tomopost";
$textToEncrypt2 = "1234";
$encryptionMethod = "AES-256-CBC";
$secretHash = "encryptionhash";
$iv = mcrypt_create_iv(16, MCRYPT_RAND);

$encryptedText = openssl_encrypt($textToEncrypt,$encryptionMethod,$secretHash, 0,$iv);
$encryptedText2 = openssl_encrypt($textToEncrypt2,$encryptionMethod,$secretHash, 0,$iv);

$decrypteduser = openssl_decrypt($encryptedText, $encryptionMethod, $secretHash, 0,$iv);
$decryptedpassword = openssl_decrypt($encryptedText2, $encryptionMethod, $secretHash, 0,$iv);
//print "<br>Username ciphertext ".$encryptedText." => ".$decrypteduser;
//print "<br>Password ciphertext ".$encryptedText2." => ".$decryptedpassword;
$client->setCredentials($encryptedText, $encryptedText2, "basic");
		// $resultAuthentication = "";
		//Call function from server
 if(isset($_POST['Operator'])){
      selectsevice("get_operators");
    }
 if(isset($_POST['Routes'])){
       selectsevice("get_routes");
    }
 if(isset($_POST['Timetable'])){
      selectsevice("get_timetable");
    }

function selectsevice($service){
		global $client,$iv ;	
		//print (base64_encode($iv));
		//print $test;
		$result = $client->call($service,array('id' => base64_encode($iv))); 

		if ($client->fault) {
		    echo "<h2>Fault</h2><pre>";
		    print_r($result);
		    echo "</pre>";
		} else {
		    $error = $client->getError();
		    if ($error) {
		        echo "<h2>Error</h2><pre>" . $error . "</pre>";
		    } else {
		        echo "<h2></h2>";
		        echo $result;
		    }
		}
}    
