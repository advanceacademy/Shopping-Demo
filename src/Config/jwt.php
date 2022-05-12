<?php

return [
    'private' => env('JWT_PRIVATE_FILE', __DIR__ . '/../../storage/private.pem'),
    'public' => env('JWT_PUBLIC_FILE', __DIR__ . '/../../storage/public.pem'),
    'algorithm' => env('JWT_ALGORITHM', 'RS256'),
];
