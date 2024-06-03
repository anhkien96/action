<?php

namespace Controller\Error;

class Http extends \Controller\Base
{
    public function error404()
    {
        // header('HTTP/1.0 404 Not Found');
        // -> wrap vào, có gì gắn kém theo, length liếc, gzip, mã hóa gì nó dễ
        echo '404 Not Found';
    }
}