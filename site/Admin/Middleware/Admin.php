<?php

namespace site\addmin\Middleware;

class Admin {

    public function handle($next) {
        // check kiểu file exist controller middleware của controller cụ thể, link động từng phần
        $req = \Reg::get('request');
        $control = $req->getController();
        $file = __SITE.$req->site().'/Middleware/Controller/'.str_replace('\\', '/', $control).'.php';
        if (is_file($file)) {
            include($file);
        }

        return $next();
    }
}

// cũng chẳng cần kiểu này

// hoặc bắn event, register, listen event