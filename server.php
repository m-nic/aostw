<?php

require_once __DIR__ . '/vendor/autoload.php';

$server = new App\Http\SoapServer(web_base_path('server.php'));

$server->registerService(App\Services\HelloService::class);

if (isset($_GET['wsdl'])) {
    header("Content-Type: text/xml");
    echo $server->get_wsdl();
} else {
    $server->handle_call();
}