<?php

namespace Middle;

class Auth {

    public function handle($next) {
        echo 123;
        $res = $next();
        echo 456;
        return $res;
    }
}