<?php

class FileRaw {
    public $name, $type, $tmp_name, $error, $size;
}

class Request {

    protected static $files = [], $format_file = true;
    protected $except_trim = [], $param = [], $is_admin = false, $is_api = false, $base, $controller, $action;

    public function __construct($option = []) {
        if (isset($option['except_trim'])) {
            foreach ($option['except_trim'] as $key) {
                $this->except_trim[] = $key;
            }
        }
        static::formatFiles();
    }

    public function withContext($option = []) {
        return new static($option);
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

    public function getBase() {
        return $this->base;
    }

    public function setBase($base) {
        $this->base = $base;
    }

    public function getController() {
        return $this->controller;
    }

    public function setController($controller) {
        $this->controller = $controller;
    }

    public function getAction() {
        return $this->action;
    }

    public function setAction($action) {
        $this->action = $action;
    }

    protected function getValue($data = [], $key, $default = '') {
        $_ = explode('.', $key);
        $val = &$data;
        foreach ($_ as $key) {
            if (isset($val[$key])) {
                $val = &$val[$key];
            }
            else {
                return $default;
            }
        }
        return $val;
    }

    protected function formatInput($vals, $key) {
        if (is_array($vals)) {
            $res = [];
            foreach ($vals as $t_key => &$val) {
                $res[$t_key] = $this->formatInput($val, $key);
            }
            return $res;
        }
        if (is_string($vals) && !in_array($key, $this->except_trim)) {
            $vals = trim($vals);
        }
        return $vals;
    }

    public function input($key, $default = '', $src = []) {
        if (!$src) {
            $src = &$_REQUEST;
        }
        $val = $this->formatInput($this->getValue($src, $key, $default), $key);
        return is_array($val)? $default: $val;
    }

    public function inputList($key, $default = [], $src = []) {
        if (!$src) {
            $src = &$_REQUEST;
        }
        $vals = $this->formatInput($this->getValue($src, $key, $default), $key);
        if (is_array($vals)) {
            $res = [];
            foreach ($vals as $key => &$val) {
                if (!is_array($val)) {
                    $res[] = $val;
                }
            }
            return $res;
        }
        return $default;
    }

    public function inputRaw($key, $default = '', $src = []) {
        if (!$src) {
            $src = &$_REQUEST;
        }
        return $this->formatInput($this->getValue($src, $key, $default), $key);
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

    protected function getFileAttr($file, $attr, $key = '') {
        if ($key !== '') {
            $_ = explode('.', $key);
            $val = $file[$attr];
            foreach ($_ as $key) {
                $val = $val[$key];
            }
            return $val;
        }
        return $file[$attr];
    }

    protected function addFiles($file, $f_key, $t_key = '') {
        $p_name = $this->getFileAttr($file, 'name', $t_key);
        if (is_array($p_name)) {
            foreach ($p_name as $key => $name) {
                $this->addFiles($file, $f_key, $t_key? $t_key.'.'.$key: $key);
            }
        }
        else {
            $item = new \FileRaw();
            foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $attr) {
                $item->$attr = $this->getFileAttr($file, $attr, $t_key);
            }
            $files = &static::$files;
            if ($t_key !== '') {
                $_ = explode('.', $f_key.'.'.$t_key);
                $last = array_pop($_);
                foreach ($_ as $key => $part) {
                    if (empty($files[$part])) {
                        $files[$part] = [];
                    }
                    $files = &$files[$part];
                }
            }
            else {
                $last = $f_key;
            }
            $files[$last] = $item;
        }
    }

    protected function formatFiles() {
        if (static::$format_file && $_FILES) {
            static::$format_file = false;
            foreach ($_FILES as $key => &$file) {
                $this->addFiles($file, $key);
            }
        }
    }

    // protected function formatFiles() {
    //     if (static::$format_file && $_FILES) {
    //         static::$format_file = false;
    //         $attr_keys = ['name', 'type', 'tmp_name', 'error', 'size'];
    //         foreach ($_FILES as $key => &$file) {
    //             if (is_array($file['name'])) {
    //                 $size = count($file['name']);
    //                 for ($i=0; $i<$size; $i++) {
    //                     if (!is_array($file['name'][$i])) {
    //                         $item = new \FileRaw();
    //                         foreach ($attr_keys as $attr) {
    //                             $item->$attr = $file[$attr][$i];
    //                         }
    //                         static::$files[$key][] = $item;
    //                     }
    //                 }
    //             }
    //             else {
    //                 $item = new \FileRaw();
    //                 foreach ($attr_keys as $attr) {
    //                     $item->$attr = $file[$attr][$i];
    //                 }
    //                 static::$files[$key] = $item;
    //             }
    //         }
    //     }
    // }

    public function file($key) {
        return isset(static::$files[$key]) && is_object(static::$files[$key]) ? static::$files[$key] : null;
    }

    public function fileList($key) {
        return isset(static::$files[$key]) && is_array(static::$files[$key]) ? array_filter(static::$files[$key], 'is_object') : [];
    }

    // public function all() {
    //     // chac phai de quy, phan biet theo key, value
    //     $handle = $this->handle;
    //     $data = [];
    //     foreach ($_POST as $key => &$value) {
    //         $data[$key] = $handle($value, $key);
    //     }
    //     foreach (static::$files as $key => &$file) {
    //         $data[$key] = $file;
    //     }
    //     return $data;
    // }

    public function all() {
        $data = [];
        foreach ($_POST as $key => &$value) {
            $data[$key] = $this->postRaw($key);
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