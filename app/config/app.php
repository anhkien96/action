<?php

return [
    'debug' => true,
    'timezone' => 'Asia/Ho_Chi_Minh',
    'middle' => [
        '\Middle\Logger',
        '\Middle\Auth',
    ],
    'error' => [
        '404' => ['control' => 'Error', 'action' => 'error404']
    ],
    'lang' => [
        'default' => 'vi'
    ],
];