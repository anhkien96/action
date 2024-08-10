<?php

namespace Controller;

class Index extends \Base\Controller {

    public function kien_test() {
        \Factory::query()->table('option')->createMany([
            ['name' => '1', 'value' => '1'],
            ['name' => '2', 'value' => '2']
        ]);

        $data = \Factory::query()->table('product')->select('*')->getAll();
        var_dump($data);
    }

    public function demo_repo() {
        // $action_service = \Loader::service('Admin::Action/Create');
        // $action_service = \Loader::service('Admin::Action_Create');

        $action_repo = \Loader::repo('Action');
        $action_repo->query()->option(['with_tag' => true]);

        $action_service = \Loader::service('Admin::Action');
        // $t = microtime(true);
        // try {
        //     $action_service->scanAction();
        // }
        // catch(Exception $e) {
            
        // }
        $action = $action_service->select('name')->get();
        var_dump($action);
        // $action = $action_service->reset()->get();
        // return $action;
        $action = $action_service->get();
        var_dump($action);
        // echo microtime(true) - $t;

        // ----

        // $repo = \Loader::repo('Action');
        // $action_list = $repo->query()->select('*')->getAll();

        // repo là duy nhất
        // mỗi lần gọi query, tạo mỗi query mới, làm cách này OK hơn, tách rời được luôn
        // giải quyết luôn được vấn đề

        // kết hợp ý tưởng Laravel Scope, hay lắm, để tiền xử lý, scope tách rời, tái sử dụng
    }

    public function index() {
        // $query = \Factory::query()->table('product')->select('*');

        // $query = \Model\Repo\Product::instance()->select('*');

        // $query = (new \Model\Query\Product())->select('*');
        // $query = \Model\Query\Product()::create()->select('*');

        // $product_viewmodel = \Model\View\Product::instance();
        // $product_viewmodel->setQuery($query);

        // $product_entity = \Model\Entity\Product::instance();
        // $product_entity->setQuery($query);

        // \Model\Entity\Product::query($query)->getList();

        // $products = \Model\Repo\Product::instance()->select('*')->getAll();

        $products = \Loader::repo('Product')->select('*')->getAll();

        // $products = \Model\Collection\Product::query($query)->load();

        // $productCollection = \Model\Collection\Product::load($products);

        // ---
        // dùng factory tạo collection theo tên nhé, không tạo kiểu này, không linh động, khó sửa code

        $productCollection = \Factory::collection('Product')->setItems($products);

        // trường hợp collection không tồn tại thì load collection base
        // áp dụng cho cả phân hệ khác
        // dùng loader tốt hơn nhỉ
        // Factory kiểu phải tạo mới, nghe Loader tường mình hơn
        
        // -> Factory cho tạo mới
        // -> Loader cho singleton

        // create, construction nên tách biệt ra khỏi lớp bản thể
        // ---

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