<?php

namespace Lib;

class Validator {

    protected $validate, $data = [], $errors = [], $type_list = ['int', 'text', 'float', 'file'];

    public function setData($data) {
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }

    public function setValidate($validate) {
        $this->validate = $validate;
    }

    public function getValidate() {
        if (!$this->validate) {
            $this->setValidate(\Reg::create('validate'));
            $this->validate->setValidate($this);
        }
        return $this->validate;
    }

    public function getValue($key) {
        $_ = explode('.', $key);
        $val = &$this->data;
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
        $validate = $this->getValidate();

        foreach ($rules as $key => $rule) {
            if (is_string($rule)) {
                $rule = explode('|', $rule);
            }

            $value = $this->getValue($key);

            $is_required = false;
            $is_list = false;
            $value_type = '';

            $diff_rule = [];

            if (in_array('required', $rule)) {
                $diff_rule['required'] = true;
                $is_required = true;
            }
            if (in_array('list', $rule)) {
                $diff_rule['list'] = true;
                $is_list = true;
            }
            foreach ($rule as $rule_value) {
                if (in_array($rule_value, $this->type_list)) {
                    $value_type = $rule_value;
                    $diff_rule[$rule_value] = true;
                    break;
                }
            }

            if ($diff_rule) {
                $_rule = [];
                foreach ($rule as $rule_key => &$rule_value) {
                    if (!(is_string($rule_value) && isset($diff_rule[$rule_value]))) {
                        $_rule[$rule_key] = $rule_value;
                    }
                }
                $rule = &$_rule;
            }

            $check = true;
            if ($is_required && (!$value || ($is_list && !is_array($value)))) {
                $this->addError($key, 'required');
                $check = false;
            }

            if ($check && $value_type) {
                $handle = [$validate, $value_type];
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
                    if (is_callable($rule_value)) {
                        $rule_value($value, $this);
                    }
                    else {
                        if (is_numeric($rule_type)) {
                            $_ = explode(':', $rule_value, 2);
                            $rule_type = $_[0];
                            $rule_value = isset($_[1]) ? $_[1] : '';
                        }
                        if (is_callable($rule_value)) {
                            $rule_value = $rule_value($value, $key);
                        }
        
                        $handle = [$validate, $rule_type];
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
        return !$this->errors;
    }

    public function addError($key, $rule_type, $rule_value = '') {
        $this->errors[$key][] = [$rule_type, $rule_value];
    }

    public function getErrors() {
        return $this->errors;
    }
}
