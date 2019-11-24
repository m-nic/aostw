<?php

namespace App\Http;

class Client
{
    private $client;

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

        return $result->{$resultKey};
    }
}