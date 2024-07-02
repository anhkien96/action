<?php

namespace Controller;

class Index extends \Controller\Base {

    public function index() {
        $this->view->render('index');
    }

    public function kien() {
        $action_repo = \Repo\Action::instance();
        // var_dump($action_repo->scanAction());
        $action_repo->create([
            'name' => 'Kiên',
            'action' => 'Xây dựng bộ tìm kiếm'
        ]);
    }

    public function hello() {
        $this->view->name = 'Kiên Nguyễn';
        $this->view->age = 28;
        // return $this->view->display('index');
        return $this->view->json();
    }
}