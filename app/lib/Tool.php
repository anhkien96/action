<?php

namespace Lib;

class Tool {

    public static function sizeConvert($value) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $value = strtoupper($value);
        $number = floatval($value);
        $unit = preg_replace('/[^A-Z]/', '', $value);
    
        if (in_array($unit, $units)) {
            return $number * pow(1024, array_search($unit, $units));
        }
        return $number;
    }
}