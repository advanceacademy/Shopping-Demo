<?php

namespace App;

use App\Helpers\Application;

require_once(__DIR__ . '/../vendor/autoload.php');

$app = new Application(__DIR__);

$app->routeGroup('/', '\App\Controllers\HomeController', [
    ['GET',    '',         'index'],
]);

$app->route('GET', '/info', function() {
    phpinfo();
    die;
});

$app->routeGroup('/api/users/', '\App\Controllers\UserController', [
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

$app->run();
