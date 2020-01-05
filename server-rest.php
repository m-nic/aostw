<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Http\Rest\Router;
use App\Http\Rest\Request;

$config = app_config();

if (empty($config['database']['same_db'])) {
    $config['database']['db_name'] .= '_rest';
}

$pdoInstance = new App\Database\SqlLite($config);

Router::useContainer([
    App\Services\CrudService::class => [$pdoInstance],
]);

Router::get('/users', 'App\Services\CrudService@browseUsers');
Router::get('/users/{id}', 'App\Services\CrudService@readUser');

Router::put('/users/{id}', function (Request $request, $id) use ($pdoInstance) {
    $service = new App\Services\CrudService($pdoInstance);
    return $service->editUser($id, $request->get('newData'));
});

Router::post('/users', function (Request $request) use ($pdoInstance) {
    $service = new App\Services\CrudService($pdoInstance);
    return $service->addUser($request->get('newData'));
});

Router::delete('/users/{id}', 'App\Services\CrudService@deleteUser');
Router::post('/users/reset', 'App\Services\CrudService@resetDb');


Router::get('/', function () {
    return '<h1>REST</h1>';
});

Router::handleRequests('rest');