<?php

namespace Admin\Middleware;

class Auth {

    public function handle($next) {
        return $next();
    }
}