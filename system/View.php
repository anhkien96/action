<?php

class View {

    protected $_req, $_data = [], $_path, $_layout = 'main', $_view, $_load = '';

    public function __construct() {
        $this->_req = \Reg::get('request');
        if ($this->_req->isAdmin()) {
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
            if ($this->_req->isAPI() || $this->_load == 'json') {
                return json_encode($this->_data);
            }
            if (!$this->_view) {
                $this->_view = str_replace('_', '/', $this->_req->getController()).'/'.$this->_req->getAction();
            }
            ob_start();
            if ($this->_load == 'layout') {
                $this->render('_layout/'.$this->_layout);
            }
            else {
                $this->render($this->_view);
            }
            return ob_get_clean();
        }
    }

    public function setLayout($layout = '') {
        $this->_layout = $layout;
    }

    public function setView($view) {
        $this->_view = $view;
    }

    public function mainContent() {
        $this->render($this->_view);
    }

    public function json($data = null) {
        $this->_load = 'json';
        if ($data) {
            $this->_data = &$data;
        }
    }
}