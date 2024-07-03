<?php

namespace Model\Entity;

class PublicUser extends \Model\Base\Entity {

    public function __construct($data = []) {
        $data = ['id' => 0];
        parent::__construct($data);
    }
}