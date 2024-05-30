<?php

namespace Middle;

class Logger {

    public function handle($next) {
        echo 'Log open';
        $res = $next();
        echo 'Log close';
        return $res;
    }
}