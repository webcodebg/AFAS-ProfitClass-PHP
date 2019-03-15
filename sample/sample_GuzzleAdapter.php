<?php

class GuzzleAfasAdapter implements \iPublications\Profit\Client
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var \GuzzleHttp\Psr7\Response
     */
    protected $response;

    /**
     * @var string|null
     */
    protected $responseBody;

    /**
     * @var string GET / POST
     */
    protected $method;

    /**
     * @var string url
     */
    protected $url;

    /**
     * @var array options
     */
    protected $options;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client;
    }

    public function Init()
    {
        $this->method = 'GET';
        $this->url = null;
        $this->options = [
            'protocols' => ['http'],
            'http_errors' => false,
            'allow_redirects' => true,
        ];
    }

    public function CheckClient()
    {
        return true;
    }

    public function Execute()
    {
        $this->response = $this->client->request($this->method, $this->url, $this->options);
        $this->responseBody = $this->response->getBody()->getContents();
    }

    public function SetUrl($url)
    {
        $this->url = $url;
    }

    public function SetConnectTimeout($timeout)
    {
        $this->options['connect_timeout'] = 0.001 * $timeout;
    }

    public function SetTimeout($timeout)
    {
        $this->options['read_timeout'] = 0.001 * $timeout;
    }

    public function SetUseSSL($ssl)
    {
        $this->options['protocols'] = ['https'];
    }

    public function SetSslAllowInsecure($insecure)
    {
        $this->options['verify'] = !$insecure;
    }

    public function SetPostData($data)
    {
        $this->method = 'POST';
        $this->options['body'] = $data;
    }

    public function SetHeaders($headers)
    {
        $assocHeaders = [];
        foreach ($headers as $header) {
            [$key, $value] = explode(': ', $header);
            $assocHeaders[$key] = $value;
        }

        $this->options['headers'] = array_merge(
            $this->options['headers'] ?? [],
            $assocHeaders
        );
    }

    public function SetHttpAuth($user, $pass)
    {
        $this->options['auth'] = [$user, $pass];
    }

    /**
     * @throws \iPublications\Profit\ClientException
     */
    public function SetNtlmAuth($domain)
    {
        throw new \iPublications\Profit\ClientException("NTLM not supported");
    }

    public function GetResponseBody()
    {
        return $this->responseBody;
    }

    public function GetResponseHttpCode()
    {
        return $this->response->getStatusCode();
    }
}
