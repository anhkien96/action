<?php

namespace Admin\Controller;

class Index extends \Admin\Controller\Base {

    public function index() {
        echo 'Admin Index<br/>';
        // return 123;
        $this->view->layout();
        // return $this->view->display();
    }
}