<?php

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

require_once __DIR__ . '/vendor/autoload.php';


$requestHistory = [];

$history = Middleware::history($requestHistory);
$handlerStack = HandlerStack::create();
$handlerStack->push($history);

$httpClient = new GuzzleHttp\Client([
    'base_uri' => get_url(),
    'timeout'  => 2.0,
    'handler'  => $handlerStack,
]);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reset'])) {
        $httpClient->post('/rest/users/reset');
    }

    if (isset($_POST['delete_id'])) {
        $del = $httpClient->delete('/rest/users/' . $_POST['delete_id']);
    }

    if (isset($_POST['edit_id'])) {
        $httpClient->put('/rest/users/' . $_POST['edit_id'], [
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
        $httpClient->post('/rest/users', [
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
}

$userData = json_decode($httpClient->get('/rest/users')->getBody(), true);

$requestsStack = [];
foreach ($requestHistory as $entry) {
    $apiMethod = $entry['request']->getUri()->getPath();
    $method = $entry['request']->getMethod();

    $requestsStack[] = [
        'method'   => "[{$method}] " . $apiMethod,
        'request'  => [
            'headers' => prettify_array_headers($entry['request']->getHeaders()),
            'body'    => prettify_JSON($entry['request']->getBody()),
        ],
        'response' => [
            'headers' => prettify_array_headers($entry['response']->getHeaders()),
            'body'    => prettify_JSON($entry['response']->getBody()),
        ],
    ];
}

$title = 'REST Client Demo';

include './ui/index.php';