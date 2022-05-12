<?php

return [
    'live' => (boolean) env('PAYPAL_LIVE', false),
    'client' => env('PAYPAL_CLIENT_ID', ''),
    'secret' => env('PAYPAL_SECRET_ID', ''),
];
