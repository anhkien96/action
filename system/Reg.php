<?php

trait Singleton {

    private static $__cls = [];

    protected function __construct() {}

    public static function instance() {
        $name = get_called_class();
        if (empty(self::$__cls[$name])) {
            self::$__cls[$name] = new static();
        }
        return self::$__cls[$name];
    }
}


class Reg {

    protected static $_map = [], $_obj = [];

    public static function map($key, $value) {
        self::$_map[$key] = $value;
    }

    public static function set($key, $obj) {
        self::$_obj[$key] = $obj;
    }

    public static function get($key, $param = []) {
        if (empty(self::$_obj[$key])) {
            $class = self::$_map[$key];
            self::$_obj[$key] = new $class(...$param);
        }
        return self::$_obj[$key];
    }

    public static function create($key, $param = []) {
        $class = self::$_map[$key];
        $obj = new $class(...$param);
        if (empty(self::$_obj[$key])) {
            self::$_obj[$key] = $obj;
        }
        return $obj;
    }

    // public static function db() {
    //     return self::get('db');
    // }

    public static function user() {
        return self::get('user');
    }

    public static function query($db = null) {
        $class = self::$_map['query'];
        return new $class($db);
    }
}