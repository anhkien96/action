<?php

class View {

    protected $_data = [], $_except = ['password'], $_path, $_layout = 'main', $_view;

    // dùng global except

    public function __construct() {
        $req = \Reg::get('request');
        if ($req->isAdmin()) {
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

    public function layout($layout = '') {
        include($this->_path.'_layout/'.($layout? $layout: $this->_layout).'.php');
    }

    public function setLayout($layout = '') {
        $this->_layout = $layout;
    }

    public function setView($view) {
        $this->_view = $view;
    }

    public function mainContent() {
        if (!$this->_view) {
            $req = \Reg::get('request');
            $this->_view = str_replace('_', '/', $req->getController()).'/'.$req->getAction();
        }
        include($this->_path.$this->_view.'.php');
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