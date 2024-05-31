<?php

namespace Repo;

class Base {

    use \Singleton;

    protected $db, $event;

    protected function __construct() {
        // $this->db = \Lib\DB::instance();
        $this->db = \Reg::db();
        // $this->event = \Lib\Event::instance();
    }

    public function exists($where, $param = []) {
        return $this->db->table($this->table)->exists($where, $param);
    }

    public function get($where, $param = []) {
        return $this->db->table($this->table)->get($where, $param);
    }
    
    public function getAll($where, $param = []) {
        return $this->db->table($this->table)->getAll($where, $param);
    }

    public function create($data = []) {
        return $this->db->table($this->table)->create($data);
    }

    // public function createMany($data = []) {
    //     return $this->db->table($this->table)->createMany($data);
    // }

    public function update($data = [], $where, $param = []) {
        return $this->db->table($this->table)->update($data, $where, $param);
    }

    public function delete($where, $param = []) {
        return $this->db->table($this->table)->delete($where, $param);
    }

    // public function update($data = [], $id) {
    //     return $this->db->table($this->table)->update($data, 'id = :id', ['id' => $id]);
    // }

    // public function delete($id) {
    //     return $this->db->table($this->table)->delete('id = :id', ['id' => $id]);
    // }

    public function __call($method, $param = []) {
        return $this->db->table($this->table)->$method(...$param);
    }
}