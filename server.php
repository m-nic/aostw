<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\HelloService;
use App\Http\SoapServer;

$server = SoapServer::make(get_full_url() . '/' . __FILE__)
    ->registerService(HelloService::class);


if (isset($_GET['wsdl'])) {
    header("Content-Type: text/xml");
    echo $server->get_wsdl();
} else {
    $server->handle_call();
}