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
        '404' => ['module' => 'Error', 'controller' => 'Http', 'action' => 'error404']
    ],
    'lang' => [
        'default' => 'vi'
    ],
];