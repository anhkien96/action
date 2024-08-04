<?php

namespace Lib;

class Query {

    protected $db, $repo, $table = '', $option = [], $select = '', $select_def = '*', $order = '', $order_def = '' , $limit = 0, $limit_def = 0, $offset = 0;

    // public function __construct($db = null) {
    //     $this->db = $db?? \Reg::get('db');

    //     // if (!static::$limit_def) {
    //     //     static::$limit_def = \Config::get('db.limit', 20);
    //     // }
    //     // $this->limit = static::$limit_def;
    // }

    // public function __construct($ctx) {
    //     $_ctx = clone \Reg::get('ctx');
    //     $_ctx->merge($ctx);
    //     $this->db = $_ctx->get('db')?? \Reg::get('db');
    // }

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
        $this->offset = 0;
        return $this;
    }

    public function setRepo($repo) {
        $repo->setQueryDefault($this);
        $this->repo = $repo;
        $this->table = $repo->getTable();
        return $this;
    }

    public function getRepo() {
        return $this->repo;
    }

    public function table($table) {
        if (!$this->repo) {
            $this->table = $table;
        }
        return $this;
    }

    public function option($option = []) {
        $this->option = $option;
        return $this;
    }

    public function getOption($key = '') {
        if ($key) {
            return $this->option[$key]?? '';
        }
        return $this->option;
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

    public function onlyGet($where = '', $param = [], $mode = \PDO::FETCH_ASSOC) {
        $where = $where ? ' WHERE '.$where : '';
        $sql = 'SELECT '.$this->selectValue().' FROM '.$this->table.$where.$this->orderValue().' LIMIT 1 OFFSET '.$this->offset;
        return $this->fetch($sql, $param, $mode);
    }

    public function get($where = '', $param = [], $mode = \PDO::FETCH_ASSOC) {
        if ($this->repo && method_exists($this->repo, 'getHook')) {
            return $this->repo->getHook($this, [$this, 'onlyGet'], $where, $param, $mode);
        }
        return $this->onlyGet($where, $param, $mode);
    }

    public function onlyGetAll($where = '', $param = [], $mode = \PDO::FETCH_ASSOC) {
        $where = $where ? ' WHERE '.$where : '';
        $sql = 'SELECT '.$this->selectValue().' FROM '.$this->table.$where.$this->orderValue().' LIMIT '.$this->limitValue().' OFFSET '.$this->offset;
        return $this->fetchAll($sql, $param, $mode);
    }

    public function getAll($where = '', $param = [], $mode = \PDO::FETCH_ASSOC) {
        if ($this->repo && method_exists($this->repo, 'getAllHook')) {
            return $this->repo->getAllHook($this, [$this, 'onlyGetAll'], $where, $param, $mode);
        }
        return $this->onlyGetAll($where, $param, $mode);
    }

    public function exists($where = '') {
        return $this->select(1)->get($where);
    }

    public function total($where = '') {
        $res = $this->select('COUNT(1) AS _total')->get($where);
        return $res? $res['_total'] : 0;
    }

    public function onlyCreate($data = []) {
        $keys = array_keys($data);
        return $this->exec('INSERT INTO '.$this->table.$this->makeInsertField($keys).' VALUES '.$this->makeInsertHolder($keys), $data);
    }

    public function create($data = []) {
        if ($this->repo && method_exists($this->repo, 'hookCreate')) {
            return $this->repo->hookCreate($this, [$this, 'onlyCreate'], $data);
        }
        return $this->onlyCreate($data);
    }

    public function createMany($datas = []) {
        if ($this->repo && method_exists($this->repo, 'hookCreateMany')) {
            return $this->repo->hookCreateMany($this, [$this, 'onlyCreate'], $datas);
        }
        $res = [];
        foreach ($datas as $key => $data) {
            $res[$key] = $this->create($data);
        }
        return $res;
    }

    public function onlyUpdate($where, $data = [], $param = []) {
        $where = $where ? ' WHERE '.$where : '';
        $store_keys = [];
        $store_data = [];
        foreach ($data as $key => &$val) {
            $store_keys[] = $key;
            $store_data['__'.$key] = $val;
        }
        return $this->exec('UPDATE '.$this->table.' SET '.$this->makeUpdateHolder($store_keys).$where, array_merge($store_data, $param));
    }

    public function update($where, $data = [], $param = []) {
        if ($this->repo && method_exists($this->repo, 'hookUpdate')) {
            return $this->repo->hookUpdate($this, [$this, 'onlyUpdate'], $where, $data, $param);
        }
        return $this->onlyUpdate($where, $data, $param);
    }

    public function onlyDelete($where, $param = []) {
        return $this->exec('DELETE FROM '.$this->table.' WHERE '.$where, $param);
    }

    public function delete($where, $param = []) {
        if ($this->repo && method_exists($this->repo, 'hookDelete')) {
            return $this->repo->hookDelete($this, [$this, 'onlyDelete'], $where, $param);
        }
        return $this->onlyDelete($where, $param);
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
