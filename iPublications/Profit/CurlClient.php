<?php

namespace iPublications\Profit;

class CurlClient implements Client {

    /**
     * @var mixed cURL handle
     */
    protected $curl;

    /**
     * @var string
     */
    protected $response;

    /**
     * @var object
     */
    protected $details;

    /**
     * @var array
     */
    protected $headers = [];

    public function Init() : void
    {
        if($this->curl !== null){
            curl_close($this->curl);
        }

        $this->curl = curl_init();

        $this->set(CURLOPT_RETURNTRANSFER, true);
        $this->set(CURLOPT_FOLLOWLOCATION, true);

        $this->response = null;
        $this->details = null;
        $this->headers = [];
    }

    public function CheckClient() : bool
    {
        if(ini_get('safe_mode') == 1) return false;
        return (bool) in_array('curl', get_loaded_extensions());
    }

    /**
     * @throws ClientException
     */
    public function Execute() : void
    {
        $this->set(CURLOPT_HTTPHEADER, $this->headers);

        $this->response = curl_exec($this->curl);

        if($this->response === false){
            throw new ClientException(curl_error($this->curl), curl_errno($this->curl));
        }

        $this->details = curl_getinfo($this->curl);
    }

    public function SetUrl($url)
    {
        $this->set(CURLOPT_URL, $url);
    }

    public function SetConnectTimeout($timeout)
    {
        $this->set(CURLOPT_CONNECTTIMEOUT_MS, $timeout);
    }

    public function SetTimeout($timeout)
    {
        $this->set(CURLOPT_TIMEOUT_MS, $timeout);
    }

    public function SetUseSSL($ssl)
    {
        $this->set(CURLOPT_USE_SSL, $ssl);
    }

    public function SetSslAllowInsecure($insecure)
    {
        $this->set(CURLOPT_SSL_VERIFYPEER, $insecure ? 0 : 1);
        $this->set(CURLOPT_SSL_VERIFYHOST, $insecure ? 0 : 2);
    }

    public function SetPostData($data)
    {
        $this->set(CURLOPT_POST, true);
        $this->set(CURLOPT_POSTFIELDS, $data);
    }

    public function SetHeaders($headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    public function SetHttpAuth($user, $pass)
    {
        $this->set(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->set(CURLOPT_USERPWD, "$user:$pass");
    }

    public function SetNtlmAuth($domain)
    {
        $this->set(CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        $this->set(CURLOPT_USERPWD, $domain);
    }

    public function GetResponseBody() : string
    {
        return $this->response ?? '';
    }

    public function GetResponseHttpCode()
    {
        return $this->details['http_code'];
    }

    protected function set($opt, $val)
    {
        curl_setopt($this->curl, $opt, $val);
    }

    public function __destruct()
    {
        @curl_close($this->curl);
    }
}