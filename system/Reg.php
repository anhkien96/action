<?php

// class Reg {

//     protected static $_map = [], $_one = [], $_obj = [];

//     public static function set($key, $value, $unique = true) {
//         self::$_map[$key] = $value;
//         self::$_one[$key] = $unique;
//     }

//     public static function get($key, $param = []) {
//         if (self::$_one[$key]) {
//             if (empty(self::$_obj[$key])) {
//                 $class = self::$_map[$key];
//                 self::$_obj[$key] = new $class(...$param);
//             }
//             return self::$_obj[$key];
//         }
//         $class = self::$_map[$key];
//         return new $class(...$param);
//     }

//     public static function create($key, $param = []) {
//         $class = self::$_map[$key];
//         return new $class(...$param);
//     }
// }

class Reg {

    protected static $_map = [], $_obj = [];

    public static function set($key, $value) {
        self::$_map[$key] = $value;
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
        return new $class(...$param);
    }
}