<?php

namespace Lib;

class DB extends \PDO {
    public function __construct($cfg = []) {
        if (!$cfg) {
            $cfg = \Config::get('db');
        }
        try {
            if (empty($cfg['option'])) {
                $cfg['option'] = [
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false
                ];
            }
            parent::__construct($cfg['dsn'], $cfg['username'], $cfg['password'], $cfg['option']);
        }
        catch (\PDOException $e) {
            echo $e->getMessage();
            exit();
        }
    }
}