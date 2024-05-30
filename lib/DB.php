<?php

namespace Lib;

class DB {

    protected $pdo, $table = '', $select = '*', $order = '', $limit_def, $limit, $offset = 0;

    public function __construct() {
        try {
            $info = \Config::get('db');
            if (empty($info['option'])) {
                $info['option'] = [
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false
                ];
            }
            $dsn = 'mysql:host='.$info['hostname'].';dbname='.$info['database'].(empty($info['port']) ? '' : ':'.$info['port']).';charset=utf8mb4';
            $this->pdo = new \PDO($dsn, $info['username'], $info['password'], $info['option']);
        }
        catch (\PDOException $e) {
            echo $e->getMessage();
            exit();
        }
        $this->limit_def = \Config::get('db.limit', 20);
        $this->limit = $this->limit_def;
    }

    public function getPDO() {
        return $this->pdo;
    }

    protected function reset() {
        $this->table = '';
        $this->select = '*';
        $this->offset = 0;
        $this->limit = $this->limit_def;
        $this->order = '';
    }

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
        return $this->pdo->prepare($sql);
    }

    public function exec($sql, $data = []) {
        $res = false;
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt) {
                $res = $stmt->execute($data);
            }
        }
        catch (\PDOException $e) {
            $this->pdo->rollBack();
        }
        $this->pdo->commit();
        return $res;
    }

    public function fetch($sql, $data = [], $mode = '') {
        try {
            $stmt = $this->pdo->prepare($sql);
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
            $stmt = $this->pdo->prepare($sql);
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

    // protected function formatOption($option) {
    //     if (empty($option['select'])) {
    //         $option['select'] = '*';
    //     }
    //     if (empty($option['param'])) {
    //         $option['param'] = [];
    //     }
    //     if (empty($option['mode'])) {
    //         $option['mode'] = \PDO::FETCH_ASSOC;
    //     }
    //     // $option['group'] = isset($option['group'])? ' GROUP BY '.$option['group'] : '';
    //     // $option['having'] = isset($option['having'])? ' HAVING '.$option['having'] : '';
    //     $option['order'] = isset($option['order'])? ' ORDER BY '.$option['order'] : '';
    //     return $option;
    // }

    public function get($where = '', $param = [], $mode = \PDO::FETCH_ASSOC) {
        $this->limit = 1;
        $where = $where ? ' WHERE '.$where : '';
        $sql = 'SELECT '.$this->select.' FROM '.$this->table.$where.$this->order.' LIMIT '.$this->limit.' OFFSET '.$this->offset;
        $this->reset();
        return $this->fetch($sql, $param, $mode);
    }

    public function getAll($where = '', $param = [], $mode = \PDO::FETCH_ASSOC) {
        $where = $where ? ' WHERE '.$where : '';
        $sql = 'SELECT '.$this->select.' FROM '.$this->table.$where.$this->order.' LIMIT '.$this->limit.' OFFSET '.$this->offset;
        $this->reset();
        return $this->fetchAll($sql, $param, $mode);
    }

    // public function getAll($table, $where = '', $option = []) {
    //     $option = $this->formatOption($option);
    //     if (empty($option['limit'])) {
    //         $option['limit'] = $this->limit_def;
    //     }
    //     if (empty($option['offset'])) {
    //         $option['offset'] = 0;
    //     }
    //     $where = $where ? ' WHERE '.$where : '';
    //     return $this->fetchAll('SELECT '.$option['select'].' FROM '.$this->table.' '.$where.$option['having'].$option['group'].$option['order'].' LIMIT '.$option['limit'].' OFFSET '.$option['offset'].'', $option['param'], $option['mode']);
    // }

    public function exists($where = '') {
        return $this->select(1)->get($where);
    }

    // public function exists($table, $where, $option = []) {
    //     if (empty($option['select'])) {
    //         $option['select'] = '1';
    //     }
    //     return $this->get($table, $where, $option);
    // }

    public function total($where = '') {
        $res = $this->select('COUNT(1) AS _total')->get($where);
        return $res? $res['_total'] : 0;
    }

    // public function total($table, $where, $option = []) {
    //     $field = 'COUNT(1) AS _total';
    //     if (isset($option['select'])) {
    //         $option['select'] .= ', '.$field;
    //     }
    //     else {
    //         $option['select'] = $field;
    //     }
    //     return $this->get($table, $where, $option)['_total'];
    // }

    public function create($data = []) {
        $keys = array_keys($data);
        return $this->exec('INSERT INTO '.$this->table.$this->makeInsertField($keys).' VALUES '.$this->makeInsertHolder($keys), $data);
    }

    // public function createMany($data = []) {
    //     $size = count($data);
    //     if ($size > 0) {
    //         $keys = array_keys($data[0]);
    //         return $this->exec('INSERT INTO '.$this->table.$this->makeInsertField($keys).' VALUES '.$this->createInsertManyHolder($keys, $size), $data);
    //     }
    //     return false;
    // }

    public function update($data = [], $where, $param = []) {
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

    // protected function createInsertManyHolder($keys, $size) {
    //     return substr(str_repeat($this->makeInsertHolder($keys). ', ', $size), 0, -2);
    // }

    protected function makeUpdateHolder($keys) {
        $hold = [];
        foreach ($keys as $key) {
            $hold[] = $key.'=:__'.$key; 
        }
        return implode(', ', $hold);
    }
}