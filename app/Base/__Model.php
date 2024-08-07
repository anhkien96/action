<?php

namespace Base;

class Model extends Query {

    protected $table;//, $scope = [];//, $event;

    public function __construct($table = '') {
        if ($table) {
            $this->setTable($table);
        }
        // $this->event = \Lib\Event::instance();
    }

    public functioN getSelect() {
        return '*';
    }

    public function getOrder() {
        return 'id DESC';
    }

    public function getLimit() {
        return \Config::get('db.limit', 20);
    }

    public function setTable($table) {
        $this->table = $table;
    }

    public function getTable() {
        return $this->table;
    }

    // public function addScope($name, $scope) {
    //     $this->scope[$name] = $scope;
    // }

    public function __afterSelect($query, $data) {
        
    }

    public function __afterUpdate($query, $data) {
        
    }
}


// $model = new Model();

// $model->addScope('select', 'base', function($query) {
//     $model->select($model->getSelect());
//     $model->order($model->getOrder());
//     $model->limit($model->getLimit());
// });

// $model->addScope('after_select', 'base', function($query, $data) {
//     if ($query->selectType() == 'all') {

//     }
//     else {

//     }
// });

$product_model = \Model\Product::instance();
$product_model->select('*')->order('id DESC')->limit(10);


// $query = new \Query('Product');
// $model = new \Model('Product');

// $query = \Model\Product::fetch();

class Repo  {

    protected $table;

    // public function __construct() {
    //     $this->scope = [
    //         'select' => function($query) {
    //             $query->select($this->getSelect())->order($this->getOrder())->limit($this->getLimit());
    //         }
    //     ];
    // }

    protected function __scopeSelect($query) {
        $query->select($this->getSelect())->order($this->getOrder())->limit($this->getLimit());
    }

    public function getSelect() {
        return '*';
    }

    public function getOrder() {
        return 'id DESC';
    }

    public function getLimit() {
        return \Config::get('db.limit', 20);
    }

    // protected function query($cfg = []) {
    //     $scope = $cfg['scope']?? '';
    //     $query = \Factory::query()->table($this->getTable());
    //     if (isset($this->scope[$scope])) {
    //         $this->scope[$scope]($query);
    //     }
    //     return $query;
    // }

    // protected function query($cfg = []) {
    //     $scope = $cfg['scope']?? '';
    //     $query = \Factory::query()->table($this->getTable());
    //     if ($scope) {
    //         $method = '__scope'. $scope;
    //         if (method_exists($this, $method)) {
    //             $this->$method($query);
    //         }
    //     }
    //     return $query;
    // }

    protected function query($cfg = []) {
        return \Factory::query()->table($this->getTable());
    }

    protected function selectQuery() {
        $query = $this->query();
        $query->select($this->getSelect());
        $query->order($this->getOrder());
        $query->limit($this->getLimit());
        return $query;
    }
}

class Product extends Repo {

    protected $table = 'product';

    // public function __construct() {
    //     parent::__construct();

    //     $this->scope = [
    //         // 'select' => [
    //         //     'field' => '*',
    //         //     'order' => 'id DESC',
    //         //     'limit' => \Config::get('db.limit', 20),
    //         // ],
    //         'select' => function($query) {
    //             $query->select($this->getSelect())->order($this->getOrder())->limit($this->getLimit());   
    //         }
    //     ];
    // }

    protected function __scopeSelect($query) {
        $query->select($this->getSelect())->order($this->getOrder())->limit($this->getLimit());
    }

    // protected function scopeAfterSelect($query, $data) {
        
    // }

    public function getTopProducts() {
        // return $this->query();
        // return $this->query(['scope' => 'Select'])->getAll();
        
        return $this->selectQuery()->getAll();
    }
}

// cơ chế option trong query