<?php

return [

    'driver' => env('SESSION_DRIVER', 'database'),

    'lifetime' => env('SESSION_LIFETIME', 120),

    'expire_on_close' => false,

    'encrypt' => env('SESSION_ENCRYPT', false),

    'path' => env('SESSION_PATH', '/'),

    'domain' => env('SESSION_DOMAIN', null),

    'secure' => env('SESSION_SECURE_COOKIE', null),

    'samesite' => env('SESSION_SAME_SITE', 'lax'),

    'partitioned' => false,

];