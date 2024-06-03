<?php

namespace Controller;

class Base {

    protected $view;

    public function __construct() {
        $this->view = \Reg::get('view');
    }

}