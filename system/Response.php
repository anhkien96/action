<?php

class Response {

    protected $req, $route, $view;

    public function __construct() {
        $this->req = \Reg::get('request');
        $this->route = \Reg::get('route');
        $this->view = \Reg::get('view');
    }

    public function json($data) {
        header('Content-Type: application/json');
        return json_encode($data);
    }
    
    public function handle() {
        $res = $this->route->loadApp();
        if ($res) {
            echo $this->json($res);
        }
        else {
            $type = $this->view->getResponseType();
            if ($this->req->isAPI() || $type == 'json') {
                echo $this->json($this->view->getData());
            }
            else {
                ob_start();
                if ($type == 'layout') {
                    $this->view->render('_layout/'.$this->view->getLayout());
                }
                elseif ($type == 'view') {
                    $this->view->render($this->view->getView());
                }
                echo ob_get_clean();
            }
        }
    }
}