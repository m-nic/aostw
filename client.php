<?php

require_once __DIR__ . '/vendor/autoload.php';

$wsdl = web_base_path('server.php') . '?wsdl';
$client = new \App\Http\Client($wsdl);


//dd($client->listMethods());
var_dump($client->sayHello(['firstName' => 'Nic']));