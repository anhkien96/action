<?php

namespace Middleware;

class Logger {

    public function handle($next, $request) {
        $view = \Reg::get('view');
        if (!$request->isAPI()) {
            $view->kien = 'Kien Nguyen';
        }
        // var_dump($request->getController());
        // var_dump($request->getAction());
        // var_dump($request->isAdmin());
        return $next();
    }
}
