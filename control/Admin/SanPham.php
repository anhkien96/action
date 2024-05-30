<?php

namespace Control\Admin;

class SanPham extends \Control\Admin\Base {

    public function index($request) {
        var_dump($request->getPage());
        echo 'Admin San Pham';
        echo $this->view->kien;
    }
}