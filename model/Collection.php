<?php

namespace Model;

class Collection {

    protected $model, $items = [];

    public function __construct($items = [], $model = '') {
        if ($model) {
            $this->setModel($model);
        }
        $this->setItems($items);
    }

    public function setModel($model) {
        $this->model = $model;
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
            $model = $this->model;
            $this->items[] = new $model($item);
        }
    }

    public function mapped($attr) {
        return array_column($this->items, $attr);
    }
}