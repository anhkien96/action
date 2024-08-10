<?php

class View {

    protected $_req, $_data = [], $_path, $_layout = 'main', $_view, $_load = '';

    public function __construct() {
        $this->_req = \Reg::get('request');
        $this->_path = __SITE.$this->_req->site().'/View/';
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

    public function setLayout($layout = '') {
        $this->_layout = $layout;
    }

    public function getLayout() {
        return $this->_layout;
    }

    public function setView($view) {
        $this->_view = $view;
    }

    public function getView() {
        if (!$this->_view) {
            $this->_view = str_replace('_', '/', $this->_req->getController()).'/'.$this->_req->getAction();
        }
        return $this->_view;
    }

    public function mainContent() {
        $this->render($this->getView());
    }

    public function json() {
        $this->_load = 'json';
    }

    public function getResponseType() {
        return $this->_load;
    }

    public function getData() {
        return $this->_data;
    }
}