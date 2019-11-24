<?php

require_once __DIR__ . '/vendor/autoload.php';

$debug = false;
if ($debug) {

    /** @var \App\Services\CrudService $client */
    $client = makeProxyClass(
        new App\Services\CrudService(new App\Database\SqlLite(app_config()))
    );

} else {
    $wsdl = web_base_path('server.php') . '?wsdl';
    $client = new \App\Http\Client($wsdl);
}


//var_dump($client->resetDb());
//var_dump(convertSoapArray($client->readUser(['id' => 1])));
var_dump(convertSoapArrayCollection($client->browseUsers()));
exit;
$client->editUser(['id' => 1, 'newData' => [
    'email' => 'super@qexample.com' . rand(1, 100)
]]);

var_dump($client->readUser(['id' => 1]));


var_dump($client->readUser(['id' => 1]));
var_dump($client->addUser([
    'newData' => [
        'first_name' => rand(100, 1000) . 'First',
        'last_name'  => rand(100, 1000) . 'Last',
        'email'      => rand(100, 1000) . '_test@example.com',
        'phone'      => '+40721 000 0' . rand(100, 1000),
    ]
]));

var_dump($client->browseUsers());
//var_dump($client->deleteUser(['id' => 11]));
//var_dump($client->browseUsers());
