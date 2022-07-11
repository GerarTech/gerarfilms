<?php

return [
    'services' => [
        
        'GerarFilms' => [
            'token' => env('TOKEN'),
            'tokenName' => 'api_token',

            'allowJsonToken' => true,
            'allowBearerToken' => true,
            'allowRequestToken' => true,
        ]
    ],
];
