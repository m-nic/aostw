<?php

require_once __DIR__ . '/vendor/autoload.php';

ini_set("soap.wsdl_cache_enabled", 0);

function runClient($server)
{
    $client = new Zend\Soap\Client($server . '?wsdl', ['cache_wsdl' => WSDL_CACHE_NONE]);
    $result = $client->sayHello(['firstName' => 'World']);
    echo $result->sayHelloResult;

    //var_dump($client->getFunctions());
    //exit;


    $result = $client->testCall(['firstName' => 'qwe']);
    var_dump($result);
    //
    echo $result->testCallResult;
}

//$server = 'http://localhost:8000/server.php';
$server = 'http://nicolaemihale.com/aostw/soap/server.php';
runClient($server);