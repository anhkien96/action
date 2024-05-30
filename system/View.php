<?php

class View {

    protected $_data = [], $_except = ['password'];

    // dùng global except

    public function __get($name) {
        return $this->_data[$name]?? '';
    }

    public function __set($name, $value) {
        $this->_data[$name] = $value;
    }

    public function render($name) {
        include(__ROOT.'view/'.$name.'.php');
    }

    public function jsonExcept($except = []) {
        $this->_except = $except;
    }

    public function json($option = []) {
        $except = isset($option['except']) ? $option['except'] : $this->_except;
        $data = [];
        foreach ($this->_data as $key => &$value) {
            if (!in_array($key, $option['except'])) {
                $data[$key] = $value;
            }
        }
        return json_encode($data);
    }
}