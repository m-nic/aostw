<?php

require_once __DIR__ . '/vendor/autoload.php';

$server = new App\Http\Soap\SoapServer(web_base_path('server-soap.php'));

$config = app_config();

if (empty($config['database']['same_db'])) {
    $config['database']['db_name'] .= '_soap';
}

$server->registerService([
    'class'  => App\Services\CrudService::class,
    'params' => [
        new App\Database\SqlLite($config)
    ]
]);

if (isset($_GET['wsdl'])) {
    header("Content-Type: text/xml");
    echo $server->get_wsdl();
} else {
    $server->handle_call();
}