<?php

return [
    'before_route' => [
        
    ],
    'after_route' => [
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