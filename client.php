<?php

require_once __DIR__ . '/vendor/autoload.php';

$wsdl = web_base_path('server.php') . '?wsdl';
$soapClient = new \App\Http\Client($wsdl);

//var_dump(convertSoapArrayCollection($client->browseUsers()));

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


include './ui/index.php';