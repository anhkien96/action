<?php

namespace \Middleware;

class Auth {

    public function handle($next) {
        return $next();
    }
}