<?php

return [
    'adapters' => [
        'local' => env('LARAVEL_BETTER_TEMP_URLS_LOCAL', true),
        's3' => env('LARAVEL_BETTER_TEMP_URLS_S3', true),
    ],
];
