<?php

return [
    'live' => env('PAYPAL_LIVE', false) === 'true' || env('PAYPAL_LIVE', false) === '1',
    'client' => env('PAYPAL_CLIENT_ID', ''),
    'secret' => env('PAYPAL_SECRET_ID', ''),
];
