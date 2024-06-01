<?php

namespace Model\User;

class PublicUser extends Base {

    public function __construct($data = []) {
        $data = ['id' => 0];
        parent::__construct($data);
    }
}