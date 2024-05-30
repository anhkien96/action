<?php

namespace Middle;

class Auth {

    public function handle($next) {
        echo 123;
        echo '<br/>';
        $res = $next();
        echo 456;
        echo '<br/>';
        return $res;
    }
}