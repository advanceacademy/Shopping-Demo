<?php

namespace App;

use App\Helpers\Router;
use App\Helpers\Application;

require_once(__DIR__ . '/../vendor/autoload.php');

$router = new Router;
$router->group(['controller' => '\App\Controllers\HomeController', 'prefix' => '/'], [
    ['GET',    '',         'index'],
]);

$router->group(['controller' => '\App\Controllers\UserController', 'prefix' => '/api/users'], [
    ['POST',   '',         'store'],
    ['GET',    '',         'listUsers'],
    ['GET',    '[i:id]',   'show'],
    ['PUT',    '[i:id]',   'update'],
    ['GET',    'profile',  'showProfile'],
    ['DELETE', '[i:id]',   'destroy'],
    ['POST',   'token',    'getToken'],
    ['POST',   'forgot',   'forgot'],
    ['POST',   'reset',    'reset'],
]);

$app = new Application($router, __DIR__);
$app->run($router);
