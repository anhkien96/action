<?php

namespace Controller;

class Index extends \Controller\Base {

    public function index() {
        // $query = \Reg::query()->table('product')->select('*');

        // $query = \Model\Repo\Product::instance()->select('*');

        // $query = (new \Model\Query\Product())->select('*');
        // $query = \Model\Query\Product()::create()->select('*');

        // $product_viewmodel = \Model\View\Product::instance();
        // $product_viewmodel->setQuery($query);

        // $product_entity = \Model\Entity\Product::instance();
        // $product_entity->setQuery($query);

        // \Model\Entity\Product::query($query)->getList();

        $repo_products = \Model\Repo\Product::instance()->select('*')->getAll();

        // $products = \Model\Collection\Product::query($query)->load();

        $products = \Model\Collection\Product::load($repo_products);

        // thêm cái default select all, default repo

        // chắc không cần Model View nữa

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