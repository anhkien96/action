<?php

namespace site\main\Middleware;

class Logger {

    public function handle($next, $req) {
        $view = \Reg::get('view');
        if (!$req->isAPI()) {
            $view->kien = 'Kien Nguyen';
        }
        // var_dump($req->getController());
        // var_dump($req->getAction());
        // var_dump($req->site());
        return $next();
    }
}
