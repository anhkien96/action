<?php

namespace Lib;

class Query {

    protected $db, $table = '', $select = '', $select_def = '*', $order = '', $order_def = '' , $limit = 0, $limit_def = 0, $skip = 0;

    public function __construct($ctx = []) {
        $this->db = $ctx['db']?? \Reg::get('db');
    }

    public function getDB() {
        return $this->db;
    }

    public function resetSelect() {
        $this->select = $this->getSelectDefault();
        return $this;
    }

    public function resetOrder() {
        $this->order = $this->getOrderDefault();
        return $this;
    }

    public function resetLimit() {
        $this->limit = $this->getLimitDefault();
        return $this;
    }

    public function reset() {
        $this->resetSelect();
        $this->resetOrder();
        $this->resetLimit();
        $this->skip = 0;
        return $this;
    }

    public function table($table) {
        $this->table = $table;
        return $this;
    }

    public function select($select) {
        $this->select = $select;
        return $this;
    }

    public function setSelectDefault($select) {
        $this->select_def = $select;
        return $this;
    }

    public function getSelectDefault() {
        return $this->select_def;
    }

    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }

    public function order($order = '') {
        $this->order = $order? ' ORDER BY '.$order: '';
        return $this;
    }

    public function setOrderDefault($order) {
        $this->order_def = $order? ' ORDER BY '.$order: '';
        return $this;
    }

    public function getOrderDefault() {
        return $this->order_def;
    }

    public function setLimitDefault($limit) {
        $this->limit_def = $limit;
        return $this;
    }

    public function getLimitDefault() {
        if (!$this->limit_def) {
            $this->setLimitDefault(\Config::get('db.limit', 20));
        }
        return $this->limit_def;
    }

    protected function selectValue() {
        return $this->select? $this->select: $this->getSelectDefault();
    }

    protected function orderValue() {
        return $this->order? $this->order: $this->getOrderDefault();
    }

    protected function limitValue() {
        return $this->limit? $this->limit: $this->getLimitDefault();
    }

    public function skip($skip) {
        $this->skip = $skip;
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
        $sql = 'SELECT '.$this->selectValue().' FROM '.$this->table.$where.$this->orderValue().' LIMIT 1 OFFSET '.$this->skip;
        return $this->fetch($sql, $param, $mode);
    }

    public function getAll($where = '', $param = [], $mode = \PDO::FETCH_ASSOC) {
        $where = $where ? ' WHERE '.$where : '';
        $sql = 'SELECT '.$this->selectValue().' FROM '.$this->table.$where.$this->orderValue().' LIMIT '.$this->limitValue().' OFFSET '.$this->skip;
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
        $keys = array_keys($datas[0]);
        $store_hold = [];
        $store_data = [];
        $n = 0;
        foreach ($datas as $row) {
            $hold = [];
            foreach ($keys as $key) {
                $name = $n.'__'.$key;
                $hold[] = ':'.$name;
                $store_data[$name] = $row[$key];
            }
            $store_hold[] = '('.implode(',', $hold).')';
            $n++;
        }
        return $this->exec('INSERT INTO '.$this->table.$this->makeInsertField($keys).' VALUES '.implode(',', $store_hold), $store_data);
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
