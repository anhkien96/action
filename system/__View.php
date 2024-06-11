<?php

class View {

    protected $_data = [], $_except = ['password'], $_path, $_layout = 'main', $_view, $_load = '';

    // dùng global except
    // except phải thuộc về model

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

    public function display($view = '') {
        $this->_load = 'view';
        $this->_view = $view;
    }

    public function layout($layout = '', $view = '') {
        $this->_load = 'layout';
        if ($layout) {
            $this->_layout = $layout;
        }
        if ($view) {
            $this->_view = $view;
        }
    }

    public function output() {
        if ($this->_load) {
            if (!$this->_view) {
                $req = \Reg::get('request');
                $this->_view = str_replace('_', '/', $req->getController()).'/'.$req->getAction();
            }
            if ($this->_load == 'layout') {
                $this->render('_layout/'.$this->_layout);
            }
            elseif ($this->_load == 'view') {
                $this->render($this->_view);
            }
        }
    }

    public function setLayout($layout = '') {
        // có thể dùng chung cho construct nên cần
        $this->_layout = $layout;
    }

    public function setView($view) {
        $this->_view = $view;
    }

    public function mainContent() {
        $this->render($this->_view);
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