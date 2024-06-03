<?php

namespace Model\User;

class Base extends \Model\Base {

    // protected $id;

    // public function setId($id) {
    //     $this->id = $id;
    // }

    // public function getId() {
    //     return $this->id;
    // }
    
    // --- 
    // có get, set từ base rồi

    public function hasRole($role) {
        return in_array($role, $this->roles);
    }

    public function hasPermission($permission) {
        return in_array($permission, $this->permissions);
    }
}