<?php

namespace iPublications\Profit;

interface Client {

    /**
     * Returns true if the pre-conditions for the client are valid,
     * e.g., PHP extensions loaded, functions available
     *
     * @return bool
     */
    public function CheckClient();

    /**
     * Call before a new request
     */
    public function Init();

    /**
     * @return void
     * @throws ClientException on connection error, **NOT** on HTTP
     */
    public function Execute();

    /**
     * @param string $url
     */
    public function SetUrl($url);

    /**
     * @param int $timeout ms
     */
    public function SetConnectTimeout($timeout);

    /**
     * @param int $timeout ms
     */
    public function SetTimeout($timeout);

    /**
     * @param bool $ssl
     */
    public function SetUseSSL($ssl);

    /**
     * @param bool $insecure
     */
    public function SetSslAllowInsecure($insecure);

    /**
     * @param string $data
     */
    public function SetPostData($data);

    /**
     * @param array $headers ['Key: Value', 'Key: Value']
     */
    public function SetHeaders($headers);

    /**
     * @param string $user
     * @param string $pass
     */
    public function SetHttpAuth($user, $pass);

    /**
     * @param $domain
     */
    public function SetNtlmAuth($domain);

    /**
     * @return string|null
     */
    public function GetResponseBody();

    /**
     * @return mixed
     */
    public function GetResponseHttpCode();

}