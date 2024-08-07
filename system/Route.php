<?php

class Route {

    protected $req, $path, $method, $map = [];//, $map_match;

    public function __construct() {
        $this->req = \Reg::get('request');
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->setPath($_SERVER['REQUEST_URI']);
    }

    protected function add($type, $src, $dest) {
        // if (empty($this->map[$src])) {
        //     $this->map[$src] = [];
        // }
        $this->map[rtrim($src, '/')][$type] = $dest;
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
        $this->req->setController($control);
        $this->req->setAction($action);

        $next = [\Reg::get('response'), 'handle'];

        $middles = array_reverse(\Config::get('app.middleware', []));
        if ($this->req->isAdmin()) {
            $middles = array_merge(array_reverse(\Config::get('app.admin_middleware', [])), $middles);
        }

        foreach ($middles as $middle) {
            $next = function () use ($middle, $next) {
                $handle = [new $middle(), 'handle'];
                return $handle($next, $this->req);
            };
        }
        $next();
    }

    protected function filterLang($seg = []) {
        if ($seg[0] == 'lang') {
            array_shift($seg);
            $this->req->setLang(array_shift($seg));
            // xử lý khi set Lang
        }
        return $seg;
    }

    protected function fitlerAppType($seg = []) {
        if ($seg && $seg[0] == 'api') {
            $this->req->setIsApi(true);
            array_shift($seg);
        }
        if ($seg && $seg[0] == 'admin') {
            $this->req->setIsAdmin(true);
            array_shift($seg);
        }
        return $seg;
    }

    protected function setPath($path) {
        $path = explode('?', $path, 2)[0];
        $path = '/'.implode('/', $this->filterLang(explode('/', ltrim($path, '/'), 3)));
        $regex = '#/page/([0-9]+)/?$#';
        if (preg_match($regex, $path, $match)) {
            $this->req->setParam('page', $match[1]);
            $path = preg_replace($regex, '/', $path);
        }
        $this->path = $path;
    }

    public function match() {
        $dest = '';
        if (isset($this->map[$this->path])) {
            $item = $this->map[$this->path];
            $dest = $item[$this->method] ?? $item['ANY'] ?? '';
        }
        else {
            foreach ($this->map as $src => $item) {
                if (preg_match('#^'.$src.'$#', $this->path, $match)) {
                    $dest = $item[$this->method] ?? $item['ANY'] ?? '';
                    if ($dest) {
                        unset($match[0]);
                        foreach ($match as $key => $val) {
                            $dest = str_replace('['.$key.']', $val, $dest);
                        }
                    }
                }
                break;
            }
        }
        $this->autoMap($dest? $dest : $this->path);
    }

    protected function parseParam($seg = [], $count = 0) {
        for ($i=2; $i<$count; $i+=2) {
            $this->req->setParam($seg[$i], $seg[$i+1]?? '');
        }
    }

    public function loadApp() {
        $handle = null;
        $is_admin = $this->req->isAdmin();
        $_ = preg_split('/[_-]/', $this->req->getController());
        $file = __APP.($is_admin? 'Admin/' : '').'Controller/'.implode('/', $_).'.php';
        if (is_file($file)) {
            include($file);
            $class = ($is_admin? '\\Admin' : '').'\\Controller\\'.implode('\\', $_);
            $action = $this->req->getAction();
            $app = new $class();
            // if ($this->map_match) {
            //     $method = $this->map_match['type'];
            //     if ($method == $this->method) {
            //         $handle = [$app, $action. '__' .$method];
            //         if (!is_callable($handle)) {
            //             $handle = [$app, $action];
            //         }
            //     }
            //     elseif ($method == 'ANY') {
            //         $handle = [$app, $action];
            //     }
            // }
            // else {

            // ----------------------------

            // check method type và check method ANY từ bên ngoài rồi, nên không cần phần trên nữa
            // cẩn thận hơn thì kiểm tra action không chứa __get, __post, __put, __delete

            $handle = [$app, $action. '__' .$this->method];
            if (!is_callable($handle)) {
                $handle = [$app, $action];
            }
            // }
        }
        if ($handle && is_callable($handle)) {
            return $handle($this->req);
        }
        return $this->error404();
    }

    protected function error404() {
        header('HTTP/1.1 404 Not Found');
        if (!$this->req->isApi()) {
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
                        return $handle($this->req);
                    }
                }
            }
        }
    }
}