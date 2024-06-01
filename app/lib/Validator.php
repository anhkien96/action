<?php

namespace Lib;

class Validator {

    protected $validate, $data = [], $errors = [], $type_list = ['int', 'text', 'float', 'file'];

    public function __construct() {
        // $this->validate = new \Lib\Validate();
        $this->validate = \Reg::get('validate');
        $this->validate->setValidator($this);
    }

    protected function setData($data) {
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }

    protected function getValue($data = [], $key) {
        $_ = explode('.', $key);
        $val = &$data;
        foreach ($_ as $key) {
            if (isset($val[$key])) {
                $val = &$val[$key];
            }
            else {
                return '';
            }
        }
        return $val;
    }
    
    public function check($rules = [], $data = []) {
        $this->setData($data);
        foreach ($rules as $key => $rule) {
            if (is_string($rule)) {
                $rule = explode('|', $rule);
            }

            $value = $this->getValue($this->data, $key);

            $is_required = false;
            $is_list = false;
            $value_type = '';

            $diff_rule = [];

            if (in_array('required', $rule)) {
                $diff_rule[] = 'required';
                $is_required = true;
            }
            if (in_array('list', $rule)) {
                $diff_rule[] = 'list';
                $is_list = true;
            }
            foreach ($rule as $rule_value) {
                if (in_array($rule_value, $this->type_list)) {
                    $value_type = $rule_value;
                    $diff_rule[] = $rule_value;
                    break;
                }
            }

            if ($diff_rule) {
                $rule = array_diff($rule, $diff_rule);
            }

            $check = true;
            if ($is_required && (!$value || ($is_list && !is_array($value)))) {
                $this->addError($key, 'required');
                $check = false;
            }

            if ($check && $value_type) {
                $handle = [$this->validate, $value_type];
                if ($is_list) {
                    foreach ($value as $item) {
                        if (!$handle($item)) {
                            $this->addError($key, 'format', $value_type);
                            $check = false;
                            break;
                        }
                    }
                }
                else {
                    if (!$handle($value)) {
                        $this->addError($key, 'format', $value_type);
                        $check = false;
                    }
                }
            }

            if ($check) {
                foreach ($rule as $rule_type => $rule_value) {
                    if (is_numeric($rule_type)) {
                        $_ = explode(':', $rule_value, 2);
                        $rule_type = $_[0];
                        $rule_value = isset($_[1]) ? $_[1] : '';
                    }
                    if (is_callable($rule_value)) {
                        $rule_value = $rule_value($value, $key);
                    }
    
                    $handle = [$this->validate, $rule_type];
                    if ($is_list) {
                        foreach ($value as $item) {
                            if (!$handle($item, $rule_value)) {
                                $this->addError($key, $rule_type, $rule_value);
                                break;
                            }
                        }
                    }
                    elseif (!$handle($value, $rule_value)) {
                        $this->addError($key, $rule_type, $rule_value);
                    }
                }
            }
        }
    }

    protected function addError($key, $rule_type, $rule_value = '') {
        $this->errors[$key][] = [$rule_type, $rule_value];
    }

    public function getErrors() {
        return $this->errors;
    }
}
