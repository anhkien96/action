<?php

namespace Control;

class Base {

    protected $view;

    public function __construct() {
        $this->view = new \View();    
    }

}