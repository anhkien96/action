<?php

namespace Control;

class Category extends Base {

    public function __construct() {
        parent::__construct();

        $this->permission = \Lib\Permission::instance();

        $this->permission->config([
            'create' => [
                'group' => 'admin',
                'permission' => 'admin.create'
            ]
        ]);

        $this->repo = \Repo\Category::instance();
    }

    public function index() {
        $this->view->render('category/index');
    }

    public function create() {
        exit;

        $min_category_len = 10;
        $categories = $_REQUEST['categories'] ?? [];
        foreach ($categories as $category) {
            if (isset($category['categoryId']) && ((strlen($category['categoryId']) >= $min_category_len) || ($category['level'] == 1))) {
                $data = [
                    'name' => trim($category['title']),
                    'src_id' => $category['categoryId'],
                    'src_parent_id' => $category['srcParentId'],
                    'level' => $category['level'],
                ];
                // $has_cat = $this->repo->select(1)->get('src_id = '.$category['categoryId']);
                // $has_cat = $this->repo->select(1)->get('src_id = :src_id', ['src_id' => $category['categoryId']]);
                $has_cat = $this->repo->exists('src_id = :src_id', ['src_id' => $category['categoryId']]);
                if (!$has_cat) {
                    $this->repo->create($data);
                }
                else {
                    echo 'Đã có: ', $category['categoryId'], ' - ', $category['title'];
                    echo "\n";
                }
            }
            else {
                echo $category['categoryId'], ' - ', $category['title'];
                echo "\n";
            }
        }
        echo 'OK';
    }

    public function update() {

    }
    
    public function delete() {

    }

    public function kien() {
        // $db = \Lib\DB::instance();
        $db = \Reg::db();
        
        // $res = $db->select('c.name, c.level')->table('category c INNER JOIN category c2 ON c.id=c2.id')->get('c.id=:id', ['id' => 3]);
        // $res = $this->repo->select('name, level')->get('id=?', [3]);
        // $res = $db->table('category')->total('id > 200 or id < 10');
        // var_dump($res);

        // $validator = new \Lib\Validator();
        $validator = \Reg::get('validator');

        $rules = [
            'name' => ['required', 'text', 'min:6'],
            'email' => ['required', 'regex' => '[a-z0-9]+@[a-z0-9]+'],
        ];

        $data = [
            'name' => ['Hahah'],
            'email' => 'anhkien96@gmail.com'
        ];

        $t = microtime(true);
        $validator->check($rules, $data);
        echo microtime(true) - $t;
        
        echo "<br/>";
        var_dump($validator->getErrors());

        $kien = '';
        if (isset($kien[1])) {
            echo 1;
        }
        else {
            echo 2;
        }

        var_dump(is_numeric('123'));

        var_dump(array_merge(['a'=>1, 'b'=>2], ['a'=>3]));

        // $req = \Request::instance();
        $req = \Reg::get('request');
        var_dump($req->all());
    }
}