<?php

namespace Middle;

class Logger {

    public function handle($next, $request) {
        var_dump($request->getControl());
        echo '<br/>';
        var_dump($request->getAction());
        echo '<br/>';
        var_dump($request->getPage());
        var_dump($request->isAdmin());
        echo '<br/>';
        echo 'Log open';
        echo '<br/>';
        $res = $next();
        echo '<br/>';
        echo 'Log close';
        echo '<br/>';
        return $res;
    }
}