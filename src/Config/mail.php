<?php

return [
    'hostname' => env('MAIL_HOSTNAME'),
    'port' => env('MAIL_PORT'),
    'secure' => env('MAIL_SECURE'),
    'charset' => env('MAIL_CHARSET'),
    'username' => env('MAIL_USERNAME'),
    'password' => env('MAIL_PASSWORD'),
    'from.address' => env('MAIL_FROM_ADRESS'),
    'from.name' => env('MAIL_FROM_NAME'),
];
