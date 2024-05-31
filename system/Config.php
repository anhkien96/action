<?php

class Config {

    protected static $map = [];

    public static function load($name) {
        static::$map[$name] = include (__APP. 'config/' . $name . '.php');
    }

    public static function get($key, $default = '') {
        $_ = explode('.', $key);
        if (empty(static::$map[$_[0]])) {
            static::load($_[0]);
        }
        $data = &static::$map;
        foreach ($_ as $part) {
            if (isset($data[$part])) {
                $data = &$data[$part];
            }
            else {
                return $default;
            }
        }
        return $data;
    }

    public static function set($key, $value) {
        $_ = explode('.', $key);
        $last = array_pop($_);
        $data = &self::$data;
        foreach ($_ as $part) {
            if (isset($data[$part])) {
                $data = &$data[$part];
            }
            else {
                $data[$part] = [];
                $data = &$data[$part];
            }
        }
        $data[$last] = $value;
    }
}