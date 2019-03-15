<?php

namespace iPublications\Profit;

interface Client {

    /**
     * Returns true if the pre-conditions for the client are valid,
     * e.g., PHP extensions loaded, functions available
     *
     * @return bool
     */
    public function CheckClient() : bool;

    /**
     * Call before a new request
     */
    public function Init() : void;

    /**
     * @return void
     * @throws ClientException on connection error, **NOT** on HTTP
     */
    public function Execute() : void;

    /**
     * @param string $url
     */
    public function SetUrl(string $url);

    /**
     * @param int $timeout ms
     */
    public function SetConnectTimeout(int $timeout);

    /**
     * @param int $timeout ms
     */
    public function SetTimeout(int $timeout);

    /**
     * @param bool $ssl
     */
    public function SetUseSSL(bool $ssl);

    /**
     * @param bool $insecure
     */
    public function SetSslAllowInsecure(bool $insecure);

    /**
     * @param string $data
     */
    public function SetPostData(string $data);

    /**
     * @param array $headers ['Key: Value', 'Key: Value']
     */
    public function SetHeaders(array $headers);

    /**
     * @param string $user
     * @param string $pass
     */
    public function SetHttpAuth(string $user, string $pass);

    /**
     * @param string $domain
     */
    public function SetNtlmAuth(string $domain);

    /**
     * @return string (empty on error)
     */
    public function GetResponseBody() : string;

    /**
     * @return mixed
     */
    public function GetResponseHttpCode();

}