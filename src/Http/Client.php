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
    //
    //public function getLastResponse()
    //{
    //    return [
    //        'method'   => $this->client->getLastMethod(),
    //        'request'  => $this->client->getLastRequest(),
    //        'response' => $this->client->getLastResponse(),
    //        'rh'       => $this->client->getLastRequestHeaders(),
    //        'respH'    => $this->client->getLastResponseHeaders(),
    //        'qwq'      => $this->client->getLastSoapOutputHeaderObjects(),
    //    ];
    //}
}