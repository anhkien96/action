<?php

namespace Controller\Page;

class Index extends \Controller\Base {

    public function index() {
        echo 'Index';
        $this->view->render('category/index');
    }

    public function kien() {
        echo 'Kien';
    }

    public function kien__get($request) {
        var_dump($request->param('page'));
        echo 'Kien get';
    }
}