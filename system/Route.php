<?php

class Route {

    protected $req, $path, $map = [];

    public function __construct() {
        $this->req = \Reg::get('request');
        $this->setPath($_SERVER['REQUEST_URI']);
    }

    protected function add($type, $src, $dest) {
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
            $seg = $this->filterSite($this->filterApi($this->filterLang(explode('/', $path))));
            if ($seg) {
                $count = count($seg);
                $control = $seg[0];
                if ($count > 1) {
                    $action = str_replace('-', '_', $seg[1]);
                    $this->parseParam($seg, $count);
                }
            }
        } else {
            $this->req->setSite();
        }

        $this->req->setController($control);
        $this->req->setAction($action);

        $next = [\Reg::get('response'), 'handle'];
        $middles = array_reverse(\Config::get('app.site.'.$this->req->site().'.middleware', []));

        foreach ($middles as $name) {
            $next = function () use ($name, $next) {
                $middle = new $name();
                return $middle->handle($next, $this->req);
            };
        }
        $next();
    }

    protected function filterLang($seg = []) {
        if ($seg && $seg[0] == 'lang') {
            array_shift($seg);
            $this->req->setLang(array_shift($seg));
            // xử lý khi set Lang
        }
        return $seg;
    }

    protected function filterApi($seg = []) {
        if ($seg && $seg[0] == 'api') {
            $this->req->setIsApi(true);
            array_shift($seg);
        }
        return $seg;
    }

    protected function filterSite($seg = []) {
        if ($seg) {
            $this->req->setSite(array_shift($seg));
        }
        else {
            $this->req->setSite();
        }
        return $seg;
    }

    protected function setPath($path) {
        $path = explode('?', $path, 2)[0];
        $path = '/'.implode('/', $this->filterApi($this->filterLang(explode('/', ltrim($path, '/'), 4))));
        $regex = '#/page/([0-9]+)/?$#';
        if (preg_match($regex, $path, $match)) {
            $this->req->setParam('page', $match[1]);
            $path = preg_replace($regex, '', $path);
        }
        $this->path = $path;
    }

    public function match() {
        $dest = '';
        if (isset($this->map[$this->path])) {
            $item = $this->map[$this->path];
            $dest = $item[$this->req->method()] ?? $item['ANY'] ?? '';
        }
        else {
            foreach ($this->map as $src => $item) {
                if (preg_match('#^'.$src.'$#', $this->path, $match)) {
                    $dest = $item[$this->req->method()] ?? $item['ANY'] ?? '';
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
        $site = $this->req->site();
        $_ = preg_split('/[_-]/', $this->req->getController());
        $file = __SITE.$site.'/Controller/'.implode('/', $_).'.php';
        if (is_file($file)) {
            include($file);
            $class = '\\site\\'.$site.'\\Controller\\'.implode('\\', $_);
            $app = new $class();
            $action = $this->req->getAction();
            $handle = [$app, $action. '__' .$this->req->method()];
            if (!is_callable($handle)) {
                $handle = [$app, $action];
            }
        }
        if ($handle && is_callable($handle)) {
            return $handle($this->req->param('id'));
        }
        return $this->error404();
    }

    protected function error404() {
        header('HTTP/1.1 404 Not Found');
        if (!$this->req->isApi()) {
            $site = $this->req->site();
            $cfg = \Config::get('app.site.'.$site.'.error.404');
            if (!$cfg) {
                $site = 'main';
                $cfg = \Config::get('app.site.'.$site.'.error.404');
            }
            $control = $cfg['controller']?? '';
            $action = $cfg['action']?? '';
            if ($control && $action) {
                $_ = preg_split('/[_-]/', $control);
                $file = __SITE.$site.'/Controller/'.implode('/', $_).'.php';
                if (is_file($file)) {
                    include($file);
                    $class = '\\site\\'.$site.'\\Controller\\'.implode('\\', $_);
                    $handle = [new $class(), $cfg['action']];
                    if (is_callable($handle)) {
                        return $handle($this->req);
                    }
                }
            }
        }
    }
}