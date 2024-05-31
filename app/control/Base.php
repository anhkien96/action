<?php

namespace Control;

class Base {

    protected $view;

    public function __construct() {
        $this->view = \Reg::get('view');
    }

}