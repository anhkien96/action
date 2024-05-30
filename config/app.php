<?php

return [
    'debug' => true,
    'timezone' => 'Asia/Ho_Chi_Minh',
    'middle' => [
        '\Middle\Auth',
        '\Middle\Logger',
    ],
    'error' => [
        '404' => ['control' => 'Error', 'action' => 'error404']
    ],
    'lang' => [
        'default' => 'vi'
    ],
];