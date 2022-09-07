<?php

declare(strict_types=1);

namespace Rmagnoprado\Rmcurl;

use Rmagnoprado\Debug\Main;

class CurlRequest
{
    private string $CACert = '';
    private string $certPublic = '';
    private string $certPrivate = '';
    private string $temppass = '';
    private bool $enableSecurity = false;

    private bool $enableProxy = false;
    private bool $proxyIP = false;
    private bool $curlProxyPort = false;
    private bool $curlProxyUser = false;
    private bool $curlProxyPass = false;

    private int $headsize;
    private int $httpcode;
    private string $request;
    private string $curlError;
    private string $info;
    private string $response;
    private string $responseHead;
    private string $responseBody;
    private string $method;
    private int $httpVersion;

    /**
     * Função Curl Para Requisição segura com Certificado Digial
     *
     * @param array<string> $header
     */
    public function request(string $url, array $header = null, string $request = null): bool
    {
        try {
            # Inicializa Curl
            $oCurl = curl_init();

            # PROXY
            if ($this->enableProxy) {
                curl_setopt($oCurl, CURLOPT_HTTPPROXYTUNNEL, 1);
                curl_setopt($oCurl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                curl_setopt($oCurl, CURLOPT_PROXY, $this->proxyIP . ':' . $this->curlProxyPort);
                if ($this->curlProxyUser !== '') {
                    curl_setopt($oCurl, CURLOPT_PROXYUSERPWD, $this->curlProxyUser . ':' . $this->curlProxyPass);
                    curl_setopt($oCurl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
                }
            }

            # Opções Curl
            curl_setopt($oCurl, CURLOPT_URL, $url);
            curl_setopt($oCurl, CURLOPT_CUSTOMREQUEST, $this->method);
            curl_setopt($oCurl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($oCurl, CURLOPT_TIMEOUT, 10 + 20);
            curl_setopt($oCurl, CURLOPT_HEADER, 1);
            curl_setopt($oCurl, CURLOPT_HTTP_VERSION, $this->httpVersion);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);

            # Opções de Segurança Curl
            if ($this->enableSecurity) {
                curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($oCurl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
                curl_setopt($oCurl, CURLOPT_SSLCERT, realpath($this->certPublic));
                curl_setopt($oCurl, CURLOPT_SSLKEY, realpath($this->certPrivate));

                if (! empty($this->temppass)) {
                    curl_setopt($oCurl, CURLOPT_KEYPASSWD, $this->temppass);
                }

                if (is_file((string) realpath($this->CACert))) {
                    curl_setopt($oCurl, CURLOPT_CAINFO, realpath($this->CACert));
                    curl_setopt($oCurl, CURLOPT_CAPATH, 'certs/');
                }
            }

            # HEADER E POST FIELDS
            if (!empty($request)) {
                curl_setopt($oCurl, CURLOPT_POST, 1);
                if($request!=null) curl_setopt($oCurl, CURLOPT_POSTFIELDS, $request);
            }

            if($header!=null) curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);

            # Seta informações da chamada
            if(isset($request) && isset($header)) {
                $this->setRequest((string) json_encode(['header' => $header,'body' => json_decode($request)]));
            }            
            $this->setResponse((string) curl_exec($oCurl));
            $this->setInfo((string) json_encode(curl_getinfo($oCurl)));
            $this->setCurlError((string) curl_error($oCurl));
            $this->setHeadSize(curl_getinfo($oCurl, CURLINFO_HEADER_SIZE));
            $this->setHttpcode(curl_getinfo($oCurl, CURLINFO_HTTP_CODE));

            # Finaliza Curl
            curl_close($oCurl);

            # Response Personalizados
            $this->setResponseHead(trim(substr($this->response, 0, (int) $this->headsize)));
            $this->setResponseBody(trim(substr($this->response, (int) $this->headsize)));
        } catch (\Exception $e) {
            $this->setCurlError($e->getMessage());
        }

        return true;
    }

    /**
     * Metodo responsável por definir o valor de $certPublic
     *
     * @return object CurlRequest
     */
    public function setCertPublic(string $certPublic): object
    {
        $this->certPublic = $certPublic;
        return $this;
    }

    /**
     * Metodo responsável por definir o valor de $certPrivate
     *
     * @return object CurlRequest
     */
    public function setCertPrivate(string $certPrivate): object
    {
        $this->certPrivate = $certPrivate;
        return $this;
    }
    /**
     * Metodo responsável por definir o valor de $CACert
     *
     * @return object CurlRequest
     */
    public function setCACert(string $CACert): object
    {
        $this->CACert = $CACert;
        return $this;
    }

    /**
     * Metodo responsável por definir o valor de $temppass
     */
    public function setTemppass(string $temppass): object
    {
        $this->temppass = $temppass;
        return $this;
    }

    /**
     * Metodo responsável por definir o valor de $request
     */
    private function setRequest(string $request): object
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Metodo responsável por definir o valor de $curlError
     */
    private function setCurlError(string $curlError): object
    {
        $this->curlError = $curlError;
        return $this;
    }

    /**
     * Metodo responsável por definir o valor de $info
     */
    private function setInfo(string $info): object
    {
        $this->info = $info;
        return $this;
    }

    /**
     * Metodo responsável por definir o valor de $response
     */
    private function setResponse(string $response): object
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Metodo responsável por definir o valor de $headsize
     */
    private function setHeadSize(int $headsize): object
    {
        $this->headsize = $headsize;
        return $this;
    }

    /**
     * Metodo responsável por definir o valor de $httpcode
     */
    private function setHttpcode(int $httpcode): object
    {
        $this->httpcode = $httpcode;
        return $this;
    }

    /**
     * Metodo responsável por definir o valor de $responseHead
     */
    private function setResponseHead(string $responseHead): object
    {
        $this->responseHead = $responseHead;
        return $this;
    }

    /**
     * Metodo responsável por definir o valor de $responseBody
     */
    private function setResponseBody(string $responseBody): object
    {
        $this->responseBody = $responseBody;
        return $this;
    }

    /**
     * Metodo responsável por habilitar a segurança
     */
    public function setEnableSecurity(bool $enable):void {
        $this->enableSecurity = $enable;
    }

     
     /**
     * Metodo responsável por setar o metódo
     */
    public function setMethod(string $method):void{
        $this->method = $method; 
    }

    /**
     * Metodo responsável por setar a versão do HTTP
     */
    public function setHttpVersion(int $version):void{
        $this->httpVersion = $version; 
    }

    /**
     * Metodo responsável por Retornar o retorno Curl
     */
    public function getResponse():string{
        return $this->responseBody; 
     }


    /**
     * Metodo responsável por Retornar o httpcode
     */
    public function getHttpCode(): int
    {
        return $this->httpcode;
    }

    /**
     * Metodo responsável por Retornar o ResponseHead
     */
    public function getResponseHead(): string
    {
        return $this->responseHead;
    }

    /**
     * Metodo responsável por Retornar o ResponseBody
     */
    public function getResponseBody(): string
    {
        return $this->responseBody;
    }

    /**
     * Metodo responsável por Retornar o request
     */
    public function getRequest(): string
    {
        return $this->request;
    }

    /**
     * Metodo responsável por Retornar o Erro Curl
     */
    public function getError(): string
    {
        return $this->curlError;
    }

    /**
     * Metodo responsável por Retornar o Info Curl
     */
    public function getInfo(): string
    {
        return $this->info;
    }
}
