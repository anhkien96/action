<?php

namespace Model\Base;

class Repo {

    use \Singleton;

    protected $query, $event;//, $_reset = true;

    protected function __construct() {
        $this->query = \Reg::query();
        $this->query->table($this->table)->order($this->getOrder());
        // $this->event = \Lib\Event::instance();
    }

    public function getOrder() {
        return 'id DESC';
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

    public function exists($where, $param = []) {
        return $this->query->exists($where, $param);
    }

    public function get($where, $param = []) {
        return $this->query->get($where, $param);
    }
    
    public function getAll($where, $param = []) {
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

    public function update($data = [], $where, $param = []) {
        return $this->query->update($data, $where, $param);
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
