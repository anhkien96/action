<?php

namespace Lib;

class DB extends \PDO {
    public function __construct($option = []) {
        if (!$option) {
            $option = \Config::get('db');
        }
        try {
            if (empty($info['option'])) {
                $info['option'] = [
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false
                ];
            }
            $dsn = 'mysql:host='.$info['hostname'].';dbname='.$info['database'].(empty($info['port']) ? '' : ':'.$info['port']).';charset=utf8mb4';
            parent::__construct($dsn, $info['username'], $info['password'], $info['option']);
        }
        catch (\PDOException $e) {
            echo $e->getMessage();
            exit();
        }
    }
}