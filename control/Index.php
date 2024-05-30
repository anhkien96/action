<?php

namespace Control;

class Index extends Base {

    public function index() {
        echo 'Index';
    }

    public function kien() {
        echo 'Kien';
    }

    public function kien__get($request) {
        var_dump($request->param('page'));
        echo 'Kien get';
    }
}