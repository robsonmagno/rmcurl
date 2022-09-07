<?php

declare(strict_types=1);

namespace Rmagnoprado\Rmcurl;

require 'vendor/autoload.php';

use Rmagnoprado\Debug\Main;
use Rmagnoprado\Rmcurl;

$debug = new Main();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<title>RM Curl</title>
    <?php echo $debug->getHeader(); ?>
</head>
<body>
<h1>RM Curl Request</h1>
<?php 

//Chaves
$CLIENT_CERT_PRIVATE = 'certs/cert_SAND.key';
$CLIENT_CERT_PUBLIC = 'certs/cert_SAND.pem';
$CLIENT_CA_CERT = 'certs/cacert.pem';

$url = 'https://secure.sandbox.api.pagseguro.com/pix/oauth2';
$request = '{
    "grant_type":"client_credentials",
    "escope":"pix.write pix.read cob.write cob.read webhook.write webhook.read payloadlocation.write payloadlocation.read"
}';
    
$header = array(
    "Content-type" => "application/json",
    "Authorization" => "Basic {{df006a4a-27b9-11ed-a261-0242ac120002:df006c5c-27b9-11ed-a261-0242ac120002}}",
    "Accept" => "application/json",
    "Cache-Control" => "no-cache",
    "Pragma" => "no-cache",
    "grant_type" => "client_credentials",
    "escope" => "pix.write pix.read cob.write cob.read webhook.write webhook.read payloadlocation.write payloadlocation.read"
);     
$cReq = new CurlRequest();
$cReq->setCACert($CLIENT_CA_CERT);
$cReq->setCertPublic($CLIENT_CERT_PUBLIC);
$cReq->setCertPrivate($CLIENT_CERT_PRIVATE);
$cReq->setTemppass("");
$cReq->setMethod('GET');
$cReq->setEnableSecurity(true);
$cReq->request($url, $header, $request);

//Funções de captura funcioando, mas não usadas
/*
$cReq->getResponse();
$cReq->getRequest();
$cReq->getError();
$cReq->getInfo();
*/
echo $debug->getBody();
?>
<script>
<?php //$debug->getScript($json); ?>
</script></body></html>