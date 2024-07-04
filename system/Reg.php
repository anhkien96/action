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

class Factory {

    protected static function create($type, $name, ...$param) {
        $_ = explode('::', $name, 2);
        if (isset($_[1])) {
            $mod = $_[0];
            $name = $_[1];
            $file = __APP.$mod.'/Model/'.$type.'/'.$name.'.php';
            if (is_file($file)) {
                include_once ($file);
                $class = '\\'.$mod.'\\Model\\'.$type.'\\'.$name;
                return new $class(...$param);
            }
            return self::create($type, $name, ...$param);
        }
        else {
            $file = __APP.'Model/'.$type.'/'.$name.'.php';
            if (is_file($file)) {
                include_once ($file);
                $class = '\\Model\\'.$type.'\\'.$name;
            }
            else {
                $class = '\\Model\\Base\\'.$type;
            }
            return new $class(...$param);
        }
    }

    public static function entity($name, ...$param) {
        return static::create('Entity', $name, ...$param);
    }

    public static function collection($name, ...$param) {
        return static::create('Collection', $name, ...$param);
    }
}

class Loader {

    public static function repo($name) {
        $_ = explode('::', $name, 2);
        if (isset($_[1])) {
            $mod = $_[0];
            $name = $_[1];
            $file = __APP.$mod.'/Model/Repo/'.$name.'.php';
            if (is_file($file)) {
                include_once ($file);
                $class = '\\'.$mod.'\\Model\\Repo\\'.$name;
                return $class::instance();
            }
            return self::repo($name);
        }
        else {
            $file = __APP.'Model/Repo/'.$name.'.php';
            if (is_file($file)) {
                include_once ($file);
                $class = '\\Model\\Repo\\'.$name;
                return $class::instance();
            }
            $class = '\\Model\\Base\\Repo';
            return $class::instance(\Lib\Text::underscore($name));
        }
    }

    public static function service($name) {
        $_ = explode('::', $name, 2);
        if (isset($_[1])) {
            $mod = $_[0];
            $name = $_[1];
            $file = __APP.$mod.'/Model/Service/'.$name.'.php';
            if (is_file($file)) {
                include_once ($file);
                $class = '\\'.$mod.'\\Model\\Service\\'.$name;
                return $class::instance();
            }
            return self::service($name);
        }
        else {
            $file = __APP.'Model/Service/'.$name.'.php';
            if (is_file($file)) {
                include_once ($file);
                $class = '\\Model\\Service\\'.$name;
                return $class::instance();
            }
            return self::repo($name);
        }
    }

    // protected static function load($type, $name) {
    //     $_ = explode('::', $name, 2);
    //     if (isset($_[1])) {
    //         $mod = $_[0];
    //         $name = $_[1];
    //         $file = __APP.$mod.'/'.$type.'/'.$name.'.php';
    //         if (is_file($file)) {
    //             include_once ($file);
    //             $class = '\\'.$mod.'\\'.$type.'\\'.$name;
    //             return $class::instance();
    //         }
    //         return self::load($type, $name);
    //     }
    //     else {
    //         $file = __APP.$type.'/'.$name.'.php';
    //         if (is_file($file)) {
    //             include_once ($file);
    //             $class = '\\'.$type.'\\'.$name;
    //             return $class::instance();
    //         }
    //         return null;
    //     }
    // }

    // public static function lib($name) {
    //     return self::load('Lib', $name);
    // }
}

class Reg {

    protected static $map = [], $obj = [];

    public static function map($key, $value) {
        self::$map[$key] = $value;
    }

    public static function set($key, $obj) {
        self::$obj[$key] = $obj;
    }

    public static function get($key, ...$param) {
        if (empty(self::$obj[$key])) {
            $class = self::$map[$key];
            self::$obj[$key] = new $class(...$param);
        }
        return self::$obj[$key];
    }

    public static function create($key, ...$param) {
        $class = self::$map[$key];
        $obj = new $class(...$param);
        if (empty(self::$obj[$key])) {
            self::$obj[$key] = $obj;
        }
        return $obj;
    }

    public static function user() {
        return self::get('user');
    }

    public static function query($db = null) {
        $class = self::$map['query'];
        return new $class($db);
    }
}