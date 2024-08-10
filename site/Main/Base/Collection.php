<?php

namespace Base;

class Collection {

    protected $entity, $items = [];

    public function __construct($items = [], $entity = '') {
        if ($entity) {
            $this->setEntity($entity);
        }
        $this->setItems($items);
    }

    public function setEntity($name) {
        $this->entity = $name;
    }

    public function clear() {
        $this->items = [];
    }

    public function setItems($items = []) {
        $this->clear();
        foreach ($items as $key => $value) {
            $this->add($value);
        }
    }

    public function getItems() {
        return $this->items;
    }

    public function add($item) {
        if (is_object($item)) {
            $this->items[] = $item;
        }
        else {
            // $class = $this->entity;
            // $this->items[] = new $class($item);
            $this->items[] = \Factory::entity($this->entity, $item);
        }
    }

    public function mapped($attr) {
        return array_column($this->items, $attr);
    }
}