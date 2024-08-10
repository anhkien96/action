<?php

namespace Admin\Middleware;

class Auth {

    public function handle($next) {
        return $next();
    }
}

// type user:

// ADMIN
// EDITOR
// USER
