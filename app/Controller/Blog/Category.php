<?php

namespace Controller\Blog;

class Category extends \Controller\Base {

    public function __construct() {
        parent::__construct();

        // $this->permission = \Lib\Permission::instance();

        // $this->permission->config([
        //     'create' => [
        //         'group' => 'admin',
        //         'permission' => 'admin.create'
        //     ]
        // ]);

        // $this->repo = \Repo\Category::instance();
    }

    public function index() {
        $this->view->render('category/index');
    }

    public function create() {
    }

    public function update() {

    }
    
    public function delete() {

    }
}