<?php

namespace Lib;

class Validate {

    protected $validator;

    public function setValidator($validator) {
        $this->validator = $validator;
    }

    public function int($value) {
        return is_int($value);
    }

    public function float($value) {
        return is_numeric($value);
    }

    public function text($value) {
        return is_string($value);
    }

    public function file($file) {
        return isset($file['tmp_name']) && empty($file['error']) && is_file($file['tmp_name']) && is_uploaded_file($file['tmp_name']);
    }

    public function min($value, $len) {
        return strlen($value) >= $len;
    }

    public function max($value, $len) {
        return strlen($value) <= $len;
    }

    public function email($value) {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public function regex($value, $pattern) {
        return preg_match('#'.$pattern.'#', $value);
    }

    public function not_regex($value, $pattern) {
        return !preg_match('#'.$pattern.'#', $value);
    }

    public function same($value, $field) {
        if ($this->validator) {
            $data = $this->validator->getData();
            return isset($data[$field]) && ($data[$field] == $value);
        }
        return false;
    }

    public function exists($value, $table_field) {
        return $this->_checkExists($value, $table_field);
    }

    public function not_exists($value, $table_field) {
        return !$this->_checkExists($value, $table_field);
    }

    public function size_min($file, $size) {
        return isset($file['size']) && is_numeric($file['size']) && ($file['size'] >= \Lib\Tool::sizeConvert($size));
    }

    public function size_max($file, $size) {
        return isset($file['size']) && is_numeric($file['size']) && ($file['size'] <= \Lib\Tool::sizeConvert($size));
    }

    public function mine($file, $mine = []) {
        if (is_string($mine)) {
            $mine = array_filter(explode(',', $mine), 'trim');
        }
        return isset($file['type']) && in_array($file['type'], $mine);
    }

    public function unique($value, $table_field) {
        $_ = explode('.', $table_field, 2);
        return \Lib\DB::instance()->table($_[0])->total($field.'=:value', ['value' => $_[1]]) <= 1;
    }

    protected function _checkExists($value, $table_field) {
        $_ = explode('.', $table_field, 2);
        return \Lib\DB::instance()->table($_[0])->exists($field.'=:value', ['value' => $_[1]]);
    }
}
