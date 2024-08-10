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

// class Context {

//     protected $obj = [];

//     public function set($key, $obj) {
//         $this->obj[$key] = $obj;
//     }

//     public function get($key) {
//         return $this->obj[$key] ?? null;
//     }
    
//     public function remove($key) {
//         if (isset($this->obj[$key])) {
//             unset($this->obj[$key]);
//         }
//     }

//     public function with($vals = []) {
//         $ctx = clone $this;
//         foreach ($vals as $key => $val) {
//             $ctx->set($key, $val);
//         }
//         return $ctx;
//     }
// }

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

    public static function query($ctx = []) {
        $class = self::$map['query'];
        return new $class($ctx);
    }
}

class Factory {

    protected static function create($type, $name, ...$param) {
        $_ = explode('::', $name, 2);
        if (isset($_[1])) {
            $mod = $_[0];
            $name = $_[1];
            $file = __SHARED.$mod.'/Model/'.$type.'/'.$name.'.php';
            if (is_file($file)) {
                include_once ($file);
                $class = '\\'.$mod.'\\Model\\'.$type.'\\'.$name;
                return new $class(...$param);
            }
            return self::create($type, $name, ...$param);
        }
        else {
            $file = __SHARED.'Model/'.$type.'/'.$name.'.php';
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

    public static function query($ctx = []) {
        return \Reg::query($ctx);
    }

    // public static function __callStatic($name, $param = []) {
    //     return \Reg::create($name, ...$param);
    // }

    public static function validator() {
        return \Reg::create('validator');
    }

    public static function validate() {
        return \Reg::create('validate');
    }

    // public static function repo($name) {
    //     $_ = explode('::', $name, 2);
    //     if (isset($_[1])) {
    //         $mod = $_[0];
    //         $name = $_[1];
    //         $file = __SHARED.$mod.'/Model/Repo/'.$name.'.php';
    //         if (is_file($file)) {
    //             include_once ($file);
    //             $class = '\\'.$mod.'\\Model\\Repo\\'.$name;
    //             return new $class();
    //         }
    //         return self::repo($name);
    //     }
    //     else {
    //         $file = __SHARED.'Model/Repo/'.$name.'.php';
    //         if (is_file($file)) {
    //             include_once ($file);
    //             $class = '\\Model\\Repo\\'.$name;
    //             return new $class();
    //         }
    //         $class = '\\Model\\Base\\Repo';
    //         return new $class(\Lib\Text::underscore($name));
    //     }
    // }
}

class Loader {

    public static function repo($name) {
        $_ = explode('::', $name, 2);
        if (isset($_[1])) {
            $mod = $_[0];
            $name = $_[1];
            $file = __SHARED.$mod.'/Model/Repo/'.$name.'.php';
            if (is_file($file)) {
                include_once ($file);
                $class = '\\'.$mod.'\\Model\\Repo\\'.$name;
                return $class::instance();
            }
            return self::repo($name);
        }
        else {
            $file = __SHARED.'Model/Repo/'.$name.'.php';
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
            $file = __SHARED.$mod.'/Model/Service/'.$name.'.php';
            if (is_file($file)) {
                include_once ($file);
                $class = '\\'.$mod.'\\Model\\Service\\'.$name;
                return $class::instance();
            }
            return self::service($name);
        }
        else {
            $file = __SHARED.'Model/Service/'.$name.'.php';
            if (is_file($file)) {
                include_once ($file);
                $class = '\\Model\\Service\\'.$name;
                return $class::instance();
            }
            return self::repo($name);
        }
    }

    public static function user() {
        return \Reg::get('user');
    }

    // protected static function load($type, $name) {
    //     $_ = explode('::', $name, 2);
    //     if (isset($_[1])) {
    //         $mod = $_[0];
    //         $name = $_[1];
    //         $file = __SHARED.$mod.'/'.$type.'/'.$name.'.php';
    //         if (is_file($file)) {
    //             include_once ($file);
    //             $class = '\\'.$mod.'\\'.$type.'\\'.$name;
    //             return $class::instance();
    //         }
    //         return self::load($type, $name);
    //     }
    //     else {
    //         $file = __SHARED.$type.'/'.$name.'.php';
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
