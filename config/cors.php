<?php

return [

    'paths' => ['api/*', 'login', 'logout', 'sanctum/csrf-cookie', 'user-profile'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['https://app.mypetly.co','http://localhost:3000', 'http://127.0.0.1:3000'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
