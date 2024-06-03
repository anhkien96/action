<?php

namespace Middleware;

class Logger {

    public function handle($next, $request) {
        $view = \Reg::get('view');
        // $req = \Reg::get('request');
        $view->kien = 'Kien Nguyen';
        // var_dump($request->getControl());
        // var_dump($request->getAction());
        // var_dump($request->getPage());
        // var_dump($request->isAdmin());
        $res = $next();
        return $res;
    }
}

// tùy theo control, admin, gắn trước biến sẵn cho view
// + từ middleware
// + từ controller base