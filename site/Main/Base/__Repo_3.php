<?php

namespace Base;

class Repo {

    use \Singleton;

    protected $table;//, $event;

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

    public function setQueryDefault($query) {
        $query->setSelectDefault($this->getSelect());
        $query->setOrderDefault($this->getOrder());
        $query->setLimitDefault($this->getLimit());
    }

    public function query() {
        return \Factory::query()->setRepo($this);
    }

    // public function exists($where = '', $param = []) {
    //     return $this->query()->exists($where, $param);
    // }

    // public function get($where = '', $param = []) {
    //     return $this->query()->get($where, $param);
    // }
    
    // public function getAll($where = '', $param = []) {
    //     return $this->query()->getAll($where, $param);
    // }

    public function hookGet($query, $fn, $where = '', $param = [], $mode = '') {
        // $with_tag = $query->getOption('with_tag');
        return $fn($where, $param, $mode);
    }

    public function hookCreate($query, $fn, $data = []) {
        // before
        $res = $fn($data);
        // after
        return $res;
    }

    public function hookCreateMany($query, $fn, $datas = []) {
        $res = [];
        foreach ($datas as $key => $data) {
            $res[$key] = $fn($data);
        }
        return $res;
    }

    // public function update($where, $data = [], $param = []) {
    //     return $this->query()->update($where, $data, $param);
    // }

    // public function delete($where, $param = []) {
    //     return $this->query()->delete($where, $param);
    // }

    // public function __call($method, $param = []) {
    //     return $this->query()->$method(...$param);
    // }
}
