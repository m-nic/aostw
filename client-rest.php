<?php

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

require_once __DIR__ . '/vendor/autoload.php';


$requestHistory = [];

$history = Middleware::history($requestHistory);
$handlerStack = HandlerStack::create();
$handlerStack->push($history);

$restClient = new GuzzleHttp\Client([
    'base_uri' => get_url(),
    'timeout'  => 2.0,
    'handler'  => $handlerStack,
]);

// @TODO add propper error handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['reset'])) {
            $restClient->post('/rest/users/reset');
        }

        if (isset($_POST['delete_id'])) {
            $del = $restClient->delete('/rest/users/' . $_POST['delete_id']);
        }

        if (isset($_POST['edit_id'])) {
            $restClient->put('/rest/users/' . $_POST['edit_id'], [
                'json' => [
                    'newData' => [
                        'first_name' => $_POST['first_name'],
                        'last_name'  => $_POST['last_name'],
                        'email'      => $_POST['email'],
                        'phone'      => $_POST['phone'],
                    ]
                ]
            ]);
        }

        if (isset($_POST['add'])) {
            $restClient->post('/rest/users', [
                'json' => [
                    'newData' => [
                        'first_name' => $_POST['first_name'],
                        'last_name'  => $_POST['last_name'],
                        'email'      => $_POST['email'],
                        'phone'      => $_POST['phone'],
                    ]
                ]
            ]);
        }
    } catch (Throwable $e) {

    }
}

try {
    $userData = json_decode($restClient->get('/rest/users')->getBody(), true);
} catch (Throwable $e) {
    $userData = [];
}

$requestsStack = formatGuzzleRequests($requestHistory);

$title = 'REST Client Demo';

include './ui/index.php';