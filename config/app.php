<?php

return [
    'debug' => true,
    'timezone' => 'Asia/Ho_Chi_Minh',
    'middleware' => [
        '\Middleware\Logger',
    ],
    'admin_middleware' => [
        '\Admin\Middleware\Auth',
    ],
    'error' => [
        '404' => ['controller' => 'Error_Http', 'action' => 'error404']
    ],
    'lang' => [
        'default' => 'vi'
    ],
];