<?php

class View {

    protected $_data = [], $_except = ['password'], $_path;

    // dùng global except

    public function __construct() {
        if (\Reg::get('request')->isAdmin()) {
            $this->_path = __APP.'Admin/View/';
        }
        else {
            $this->_path = __APP.'View/';
        }
    }

    public function __get($name) {
        return $this->_data[$name]?? '';
    }

    public function __set($name, $value) {
        $this->_data[$name] = $value;
    }

    public function render($name) {
        include($this->_path.$name.'.php');
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