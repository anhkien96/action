<?php

namespace Middle;

class Auth {

    public function handle($next) {
        return $next();
    }
}