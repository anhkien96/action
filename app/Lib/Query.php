<?php

namespace Lib;

class Query {

    protected static $limit_def = 0;
    protected $db, $table = '', $select = '*', $order = '', $limit, $offset = 0;

    public function __construct($db = null) {
        $this->db = $db?? \Reg::get('db');
        if (!static::$limit_def) {
            static::$limit_def = \Config::get('db.limit', 20);
        }
        $this->limit = static::$limit_def;
    }

    public function getDB() {
        return $this->db;
    }

    public function reset() {
        $this->select = '*';
        $this->offset = 0;
        $this->limit = $this->limit_def;
    }

    // public function repo($repo) {
    // }

    public function table($table) {
        $this->table = $table;
        return $this;
    }

    public function select($select) {
        $this->select = $select;
        return $this;
    }

    public function order($order) {
        $this->order = ' ORDER BY '.$order;
        return $this;
    }

    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset) {
        $this->offset = $offset;
        return $this;
    }

    public function prepare($sql) {
        return $this->db->prepare($sql);
    }

    public function exec($sql, $data = []) {
        $res = false;
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt) {
                $res = $stmt->execute($data);
            }
        }
        catch (\PDOException $e) {
            $this->db->rollBack();
        }
        $this->db->commit();
        return $res;
    }

    public function fetch($sql, $data = [], $mode = '') {
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt) {
                $stmt->execute($data);
                return $stmt->fetch($mode? $mode: \PDO::FETCH_ASSOC);
            }
            return false;
        }
        catch (\PDOException $e) {
            return false;
        }
    }

    public function fetchAll($sql, $data = [], $mode = '') {
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt) {
                $stmt->execute($data);
                return $stmt->fetchAll($mode? $mode: \PDO::FETCH_ASSOC);
            }
            return [];
        }
        catch (\PDOException $e) {
            return [];
        }
    }

    public function get($where = '', $param = [], $mode = \PDO::FETCH_ASSOC) {
        $where = $where ? ' WHERE '.$where : '';
        $sql = 'SELECT '.$this->select.' FROM '.$this->table.$where.$this->order.' LIMIT 1 OFFSET '.$this->offset;
        // $this->reset();
        return $this->fetch($sql, $param, $mode);
    }

    public function getAll($where = '', $param = [], $mode = \PDO::FETCH_ASSOC) {
        $where = $where ? ' WHERE '.$where : '';
        $sql = 'SELECT '.$this->select.' FROM '.$this->table.$where.$this->order.' LIMIT '.$this->limit.' OFFSET '.$this->offset;
        // $this->reset();
        return $this->fetchAll($sql, $param, $mode);
    }

    public function exists($where = '') {
        return $this->select(1)->get($where);
    }

    public function total($where = '') {
        $res = $this->select('COUNT(1) AS _total')->get($where);
        return $res? $res['_total'] : 0;
    }

    public function create($data = []) {
        $keys = array_keys($data);
        return $this->exec('INSERT INTO '.$this->table.$this->makeInsertField($keys).' VALUES '.$this->makeInsertHolder($keys), $data);
    }

    public function createMany($datas = []) {
        $res = [];
        foreach ($datas as $key => $data) {
            $res[$key] = $this->create($data);
        }
        return $res;
    }

    public function update($where, $data = [], $param = []) {
        $where = $where ? ' WHERE '.$where : '';
        $store_keys = [];
        $store_data = [];
        foreach ($data as $key => &$val) {
            $store_keys[] = $key;
            $store_data['__'.$key] = $val;
        }
        return $this->exec('UPDATE '.$this->table.' SET '.$this->makeUpdateHolder($store_keys).$where, array_merge($store_data, $param));
    }

    public function delete($where, $param = []) {
        return $this->exec('DELETE FROM '.$this->table.' WHERE '.$where, $param);
    }

    protected function makeInsertField($keys) {
        return ' ('. implode(', ', $keys).')';
    }

    protected function makeInsertHolder($keys) {
        $hold = [];
        foreach ($keys as $key) {
            $hold[] = ':'.$key;
        }
        return '('.implode(', ', $hold).')';
    }

    protected function makeUpdateHolder($keys) {
        $hold = [];
        foreach ($keys as $key) {
            $hold[] = $key.'=:__'.$key; 
        }
        return implode(', ', $hold);
    }
}
