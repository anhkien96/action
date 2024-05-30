<?php

class Route {

    protected $request, $path, $method, $lang_def, $map = [], $match_item;

    public function __construct() {
        $this->lang_def = \Config::get('app.lang.default');
        $this->request = \Request::instance();
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->setPath($_SERVER['REQUEST_URI']);
    }

    protected function add($type, $src, $dest) {
        $this->map[] = ['type' => $type, 'src' => $src, 'dest' => $dest];
    }

    public function get($src, $dest) {
        $this->add('GET', $src, $dest);
    }

    public function post($src, $dest) {
        $this->add('POST', $src, $dest);
    }

    public function put($src, $dest) {
        $this->add('PUT', $src, $dest);
    }

    public function delete($src, $dest) {
        $this->add('DELETE', $src, $dest);
    }

    public function any($src, $dest) {
        $this->add('ANY', $src, $dest);
    }

    protected function autoMap($path) {
        $path = trim($path, '/');
        if ($path) {
            $seg = $this->fitlerAppType($this->filterLang(explode('/', $path)));
            if ($seg) {
                $count = count($seg);
                $control = $seg[0];
                $action = $count > 1 ? str_replace('-', '_', $seg[1]) : 'index';
                $this->parseParam($seg, $count);
            }
            else {
                $control = 'Index';
                $action = 'index';
            }
        }
        else {
            $control = 'Index';
            $action = 'index';
        }
        $this->request->setControl($control);
        $this->request->setAction($action);

        $next = [$this, 'loadApp'];
        foreach (array_reverse(\Config::get('app.middle')) as $middle) {
            $next = function () use ($middle, $next) {
                $handle = [new $middle(), 'handle'];
                $handle($next, $this->request);
            };
        }
        $next();
    }

    protected function filterLang($seg = []) {
        if ($seg[0] == 'lang') {
            array_shift($seg);
            $lang = array_shift($seg)?? $this->lang_def;
            // xử lý khi set Lang
        }
        return $seg;
    }

    protected function fitlerAppType($seg = []) {
        $type = array_shift($seg);
        if ($type == 'api') {
            $this->request->setIsApi(true);
            if ($seg && $seg[0] == 'admin') {
                $this->request->setIsAdmin(true);
                array_shift($seg);
            }
        }
        elseif ($type == 'admin') {
            $this->request->setIsAdmin(true);
        }
        elseif ($seg && $seg[0] == 'admin') {
            $this->request->setIsAdmin(true);
            array_shift($seg);
        }
        return $seg;
    }

    protected function setPath($path) {
        $path = explode('?', $path, 2)[0];
        $path = '/'.implode('/', $this->filterLang(explode('/', ltrim($path, '/'), 3)));
        if (preg_match('#/page/([0-9]+)/?$#', $path, $match)) {
            $this->request->setParam('page', $match[1]);
            $path = str_replace($match[0], '/', $path);
        }
        $this->path = $path;
    }

    public function match() {
        foreach ($this->map as $item) {
            if (preg_match('#^'.$item['src'].'$#', $this->path, $match)) {
                unset($match[0]);
                foreach ($match as $key => $val) {
                    $item['dest'] = str_replace('['.$key.']', $val, $item['dest']);
                }
                $this->match_item = $item;
                $this->autoMap($item['dest']);
                break;
            }
        }
        if (!$this->match_item) {
            $this->autoMap($this->path);
        }
    }

    protected function parseParam($seg = [], $count = 0) {
        for ($i=2; $i<$count; $i+=2) {
            $this->request->setParam($seg[$i], $seg[$i+1]?? '');
        }
    }

    public function loadApp() {
        $handle = null;
        $is_admin = $this->request->isAdmin();
        $_ = preg_split('/_-/', $this->request->getControl());
        $file = __ROOT.'control/'.($is_admin? 'Admin/': '').implode('/', $_).'.php';
        if (is_file($file)) {
            include($file);
            $class = '\\Control\\'.($is_admin? 'Admin\\': '').implode('\\', $_);
            $action = $this->request->getAction();
            if ($this->match_item) {
                $method = $this->match_item['type'];
                if ($method == 'ANY' || $method == $this->method) {
                    $handle = [new $class(), $action];
                }
            }
            else {
                $app = new $class();
                $handle = [$app, $action. '__' .$this->method];
                if (!is_callable($handle)) {
                    $handle = [$app, $action];
                }
            }
        }
        if ($handle && is_callable($handle)) {
            $handle($this->request);
        }
        else {
            $this->error404();
        }
    }

    protected function error404() {
        header('HTTP/1.1 404 Not Found');
        if (!$this->request->isApi()) {
            $cfg404 = \Config::get('app.error.404');
            $control = $cfg404['control']?? '';
            $action = $cfg404['action']?? '';
            $file = __ROOT.'control/'.$cfg404['control'].'.php';
            if (is_file($file)) {
                include($file);
                $class = '\\Control\\'.$cfg404['control'];
                $handle = [new $class(), $cfg404['action']];
                if (is_callable($handle)) {
                    $handle($this->request);
                }
            }
        }
    }
}