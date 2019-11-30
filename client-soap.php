<?php

require_once __DIR__ . '/vendor/autoload.php';

$wsdl = web_base_path('server-soap.php') . '?wsdl';
$soapClient = new \App\Http\Soap\SoapClient($wsdl);

$requestsStack = [];

$soapClient->afterCall(function ($httpRequestData) use (&$requestsStack) {
    $requestsStack[] = $httpRequestData;
});

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reset'])) {
        $soapClient->resetDb();
    }

    if (isset($_POST['delete_id'])) {
        $soapClient->deleteUser(['id' => $_POST['delete_id']]);
    }

    if (isset($_POST['edit_id'])) {
        $soapClient->editUser([
            'id'      => $_POST['edit_id'],
            'newData' => [
                'first_name' => $_POST['first_name'],
                'last_name'  => $_POST['last_name'],
                'email'      => $_POST['email'],
                'phone'      => $_POST['phone'],
            ]
        ]);
    }

    if (isset($_POST['add'])) {
        $soapClient->addUser([
            'newData' => [
                'first_name' => $_POST['first_name'],
                'last_name'  => $_POST['last_name'],
                'email'      => $_POST['email'],
                'phone'      => $_POST['phone'],
            ]
        ]);
    }
}

$userData = convertSoapArrayCollection($soapClient->browseUsers());
$title = 'Soap Client Demo';

include './ui/index.php';