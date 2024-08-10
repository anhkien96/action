<?php

return [
    'debug' => true,
    'timezone' => 'Asia/Ho_Chi_Minh',

    'site_active' => [
        'main' => 1,
        'admin' => 1
    ],
    'site_default' => 'main',

    'site' => [
        'main' => [
            'middleware' => [
                '\site\main\Middleware\Logger',
            ],
            'error' => [
                '404' => ['controller' => 'Error_Http', 'action' => 'error404']
            ],
        ],
        'admin' => [
            'middleware' => [
                '\site\admin\Middleware\Auth',
            ]
        ]
    ],
    
    'lang' => [
        'default' => 'vi'
    ],
];