<?php

class FileRaw {
    public $name, $type, $tmp_name, $error, $size;
}

class Request {

    protected static $files = [], $format_file = true;
    protected $handle = null, $param = [], $is_admin = false, $is_api = false, $control, $action;

    public function __construct($option = []) {
        if (empty($option['handle'])) {
            $this->handle = function($value, $key) {
                return is_string($value) ? trim($value) : $value;
            };
        }
        static::formatFiles();
    }

    public function withHandle($fn) {
        return new static(['handle' => $fn]);
    }

    public function isAdmin() {
        return $this->is_admin;
    }

    public function setIsAdmin($is_admin) {
        $this->is_admin = $is_admin;
    }

    public function isApi() {
        return $this->is_api;
    }

    public function setIsApi($is_api) {
        $this->is_api = $is_api;
    }

    public function getControl() {
        return $this->control;
    }

    public function setControl($control) {
        $this->control = $control;
    }

    public function getAction() {
        return $this->action;
    }

    public function setAction($action) {
        $this->action = $action;
    }

    public function input($key, $default = '', $src = []) {
        if (!$src) {
            $src = &$_REQUEST;
        }
        $handle = $this->handle;
        return isset($src[$key]) && !is_array($src[$key]) ? $handle($src[$key], $key) : $default;
    }

    public function inputList($key, $default = [], $src = []) {
        if (!$src) {
            $src = &$_REQUEST;
        }
        if (isset($src[$key]) && is_array($src[$key])) {
            $handle = $this->handle;
            $val_list = [];
            foreach ($src[$key] as &$val) {
                if (!is_array($val)) {
                    $val_list[] = $handle($val, $key);
                }
            }
            return $val_list;
        }
        return $default;
    }

    public function inputRaw($key, $default = '', $src = []) {
        if (!$src) {
            $src = &$_REQUEST;
        }
        return isset($src[$key]) ? $src[$key] : $default;
    }

    public function get($key, $default = '') {
        return $this->input($key, $default, $_GET);
    }

    public function getList($key, $default = []) {
        return $this->inputList($key, $default, $_GET);
    }

    public function getRaw($key, $default = '') {
        return $this->inputRaw($key, $default, $_GET);
    }

    public function post($key, $default = '') {
        return $this->input($key, $default, $_POST);
    }

    public function postList($key, $default = []) {
        return $this->inputList($key, $default, $_POST);
    }

    public function postRaw($key, $default = '') {
        return $this->inputRaw($key, $default, $_POST);
    }

    protected function formatFiles() {
        if (static::$format_file && $_FILES) {
            static::$format_file = false;
            $attr_keys = ['name', 'type', 'tmp_name', 'error', 'size'];
            foreach ($_FILES as $key => &$file) {
                if (is_array($file['name'])) {
                    $size = count($file['name']);
                    for ($i=0; $i<$size; $i++) {
                        if (!is_array($file['name'][$i])) {
                            $item = new \FileRaw();
                            foreach ($attr_keys as $attr) {
                                $item->$attr = $file[$attr][$i];
                            }
                            static::$files[$key][] = $item;
                        }
                    }
                }
                else {
                    $item = new \FileRaw();
                    foreach ($attr_keys as $attr) {
                        $item->$attr = $file[$attr][$i];
                    }
                    static::$files[$key] = $item;
                }
            }
        }
    }

    public function file($key) {
        return isset($_FILES[$key]) && is_object(static::$files[$key]) ? static::$files[$key] : null;
    }

    public function fileList($key) {
        return isset($_FILES[$key]) && is_array(static::$files[$key]) ? static::$files[$key] : [];
    }

    public function all() {
        $handle = $this->handle;
        $data = [];
        foreach ($_POST as $key => &$value) {
            $data[$key] = $handle($value, $key);
        }
        foreach (static::$files as $key => &$file) {
            $data[$key] = $file;
        }
        return $data;
    }

    public function param($key, $default = '') {
        $_ = explode('_', $key, 2);
        if (($_[1] ?? $_[0]) == 'id') {
            return intval($this->param[$key]);
        }
        return $this->param[$key] ?? $default;
    }

    public function setParam($key, $value) {
        $this->param[$key] = $value;
    }

    public function getPage() {
        $page = intval(empty($this->param['page'])? $this->input('page', 1): $this->param['page']);
        return $page < 1 ? 1 : $page;
    }
}