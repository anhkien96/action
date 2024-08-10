<?php

namespace Lib;

class Text {

    public static function camelCase($str) {
        $_ = array_map('ucfirst', explode('_', $str));
        return implode('', $_);
    }
    
    public static function underscore($str) {
        $str = preg_replace('/([a-z])([A-Z])/', '$1_$2', $str);
        return strtolower($str);
    }
}