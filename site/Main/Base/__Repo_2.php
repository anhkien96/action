<?php

namespace Base;

class Repo {

    use \Singleton;

    protected $table, $query;//, $event;//, $_reset = true;

    public function __construct($table = '') {
        if ($table) {
            $this->table = $table;
        }
        $this->query = \Factory::query();
        $this->query->table($this->table);
        $this->__init();
        // $this->event = \Lib\Event::instance();
    }

    protected function __init() {
        $this->query->setSelectDefault($this->getSelect());
        $this->query->setOrderDefault($this->getOrder());
        $this->query->setLimitDefault($this->getLimit());
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

    // public function reset() {
    //     $this->query->order($this->getOrder());
    // }

    // éo cần reset, tự mà reset

    // public function order($order) {
    //     $this->query->order($order);
    // }

    // public function query() {
    //     $this->db->table($this->table);
    //     return $this;
    // }

    // public function setReset($bool) {
    //     $this->_reset = $bool;
    // }

    public function exists($where = '', $param = []) {
        return $this->query->exists($where, $param);
    }

    public function get($where = '', $param = []) {
        return $this->query->get($where, $param);
    }
    
    public function getAll($where = '', $param = []) {
        return $this->query->getAll($where, $param);
    }

    public function create($data = []) {
        return $this->query->create($data);
    }

    public function createMany($datas = []) {
        $res = [];
        foreach ($datas as $key => $data) {
            $res[$key] = $this->create($data);
        }
        return $res;
    }

    public function update($where, $data = [], $param = []) {
        return $this->query->update($where, $data, $param);
    }

    public function delete($where, $param = []) {
        return $this->query->delete($where, $param);
    }

    public function __call($method, $param = []) {
        // if ($this->_reset) {
        //     $this->db->repo($this)->$method(...$param);
        // }
        // else {
        //     $this->db->$method(...$param);
        // }
        $this->query->$method(...$param);
        return $this;
    }
}
