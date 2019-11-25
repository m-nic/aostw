<?php

namespace App\Http;

class Client
{
    private $client;
    private $afterCallFn;

    public function __construct($wsdl)
    {
        $this->client = new \Zend\Soap\Client($wsdl, ['cache_wsdl' => WSDL_CACHE_NONE]);
    }

    public function listMethods()
    {
        return $this->client->getFunctions();
    }

    public function __call($name, $arguments)
    {
        $result = $this->client->call($name, $arguments);
        $resultKey = "{$name}Result";

        if (is_callable($this->afterCallFn)) {
            ($this->afterCallFn)($this->getHttpRequestData(), $name, $arguments);
        }

        return $result->{$resultKey};
    }

    function afterCall(callable $fn)
    {
        $this->afterCallFn = $fn;
    }

    public function getHttpRequestData()
    {
        return [
            'method'   => $this->client->getLastMethod(),
            'request'  => [
                'headers' => $this->client->getLastRequestHeaders(),
                'body'    => prettify_XML($this->client->getLastRequest()),
            ],
            'response' => [
                'headers' => $this->client->getLastRequestHeaders(),
                'body'    => prettify_XML($this->client->getLastResponse()),
            ],
        ];
    }
}