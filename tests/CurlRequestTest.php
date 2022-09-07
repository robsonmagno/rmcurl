<?php
declare(strict_types=1);

namespace Rmagnoprado\Rmcurl;

require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Rmagnoprado\Rmcurl\CurlRequest;

/** @CurlRequestTest */
class CurlRequestTest extends TestCase {

    public function test_request() : void{
        //Chaves
        $CLIENT_CERT_PRIVATE = 'certs/cert_SAND.key';
        $CLIENT_CERT_PUBLIC = 'certs/cert_SAND.pem';

        $url = 'https://secure.sandbox.api.pagseguro.com/pix/oauth2';
        $request = "{grant_type:client_credentials}";

        $header = array(
            "Content-type: application/json;charset=\"utf-8\"",
            "Authorization: Basic {{df006a4a-27b9-11ed-a261-0242ac120002:df006c5c-27b9-11ed-a261-0242ac120002}}",
            "Accept: application/json",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "grant_type: client_credentials",
            "escope: pix.write pix.read cob.write cob.read webhook.write webhook.read payloadlocation.write payloadlocation.read"
        );     

        $cReq = new CurlRequest();
        $cReq->setCertPublic($CLIENT_CERT_PUBLIC);
        $cReq->setCertPrivate($CLIENT_CERT_PRIVATE);
        $cReq->setMethod('GET');
        $cReq->setEnableSecurity(true);
        $cReq->setTemppass("");
        $cReq->setHttpVersion(CURL_HTTP_VERSION_1_0);
        
        $this->assertEquals(true, 
          $cReq->request($url, $header, $request)
        );

    }
}