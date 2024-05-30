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
