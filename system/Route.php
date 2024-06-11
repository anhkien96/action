<?php

class Route {

    protected $request, $path, $method, $map = [], $match_item;

    public function __construct() {
        $this->request = \Reg::get('request');
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
        $control = 'Index';
        $action = 'index';
        if ($path) {
            $seg = $this->fitlerAppType($this->filterLang(explode('/', $path)));
            if ($seg) {
                $count = count($seg);
                $control = $seg[0];
                if ($count > 1) {
                    $action = str_replace('-', '_', $seg[1]);
                    $this->parseParam($seg, $count);
                }
            }
        }
        $this->request->setController($control);
        $this->request->setAction($action);

        $next = function() {
            return $this->loadApp()?? \Reg::get('view')->output();
        };

        $middles = array_reverse(\Config::get('app.middleware', []));
        if ($this->request->isAdmin()) {
            $middles = array_merge(array_reverse(\Config::get('app.admin_middleware', [])), $middles);
        }

        foreach ($middles as $middle) {
            $next = function () use ($middle, $next) {
                $handle = [new $middle(), 'handle'];
                return $handle($next, $this->request);
            };
        }
        echo $next();
    }

    protected function filterLang($seg = []) {
        if ($seg[0] == 'lang') {
            array_shift($seg);
            $this->request->setLang(array_shift($seg));
            // xử lý khi set Lang
        }
        return $seg;
    }

    protected function fitlerAppType($seg = []) {
        if ($seg && $seg[0] == 'api') {
            $this->request->setIsApi(true);
            array_shift($seg);
        }
        if ($seg && $seg[0] == 'admin') {
            $this->request->setIsAdmin(true);
            array_shift($seg);
        }
        return $seg;
    }

    protected function setPath($path) {
        $path = explode('?', $path, 2)[0];
        $path = '/'.implode('/', $this->filterLang(explode('/', ltrim($path, '/'), 3)));
        $regex = '#/page/([0-9]+)/?$#';
        if (preg_match($regex, $path, $match)) {
            $this->request->setParam('page', $match[1]);
            $path = preg_replace($regex, '/', $path);
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

    protected function loadApp() {
        $handle = null;
        $is_admin = $this->request->isAdmin();
        $_ = preg_split('/[_-]/', $this->request->getController());
        $file = __APP.($is_admin? 'Admin/' : '').'Controller/'.implode('/', $_).'.php';
        if (is_file($file)) {
            include($file);
            $class = ($is_admin? '\\Admin' : '').'\\Controller\\'.implode('\\', $_);
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
            return $handle($this->request);
        }
        else {
            return $this->error404();
        }
    }

    protected function error404() {
        header('HTTP/1.1 404 Not Found');
        if (!$this->request->isApi()) {
            $cfg = \Config::get('app.error.404');
            $control = $cfg['controller']?? '';
            $action = $cfg['action']?? '';
            if ($control && $action) {
                $_ = preg_split('/[_-]/', $control);
                $file = __APP.'Controller/'.implode('/', $_).'.php';
                if (is_file($file)) {
                    include($file);
                    $class = '\\Controller\\'.implode('\\', $_);
                    $handle = [new $class(), $cfg['action']];
                    if (is_callable($handle)) {
                        return $handle($this->request);
                    }
                }
            }
        }
    }
}