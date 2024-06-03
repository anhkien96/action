<?php

namespace Model;

class Base {

    protected $_data = [];

    public function __construct($data = []) {
        $this->setData($data);
    }

    public function setData($data = []) {
        foreach ($data as $key => $value) {
            $method = 'set_'.$key;
            $fn = [$this, $method];
            if (is_callable($fn)) {
                $fn($value);
            }
            else {
                $this->__set($key, $value);
            }
        }
    }

    public function __set($key, $value) {
        $this->_data[$key] = $value;
    }

    public function __get($key) {
        return $this->_data[$key]?? '';
    }

    // public static function load($data) {
    //     return new static($data);
    // }
}