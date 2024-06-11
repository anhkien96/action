<?php

namespace Lib;

class Mail {

    protected $subject, $body, $from, $to = [], $cc = [], $bcc;

    public function __construct() {
        
    }

    public function setFrom($from) {
        $this->from = $from;
        return $this;
    }

    public function setTo($to) {
        $this->to = $to;
        return $this;
    }

    public function setCC($cc) {
        $this->cc = $cc;
        return $this;
    }

    public function setBCC($bcc) {
        $this->bcc = $bcc;
        return $this;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }

    public function setBody($body) {
        $this->body = $body;
        return $this;
    }

    public function send() {
        
    }
}