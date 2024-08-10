<?php

namespace Model\Repo;

class Action extends \Base\Repo {

    protected $table = 'action';

    protected function scanControl($dir, $prefix = '', $level = 0) {
        $res = [];
        foreach (scandir($dir) as $name) {
            if (in_array($name, ['.', '..', 'Base.php'])) {
                continue;
            }
            $file = $dir.'/'.$name;
            $path = $prefix? $prefix.'/'.$name : $name;
            if (is_dir($file)) {
                $res = array_merge($res, $this->scanControl($file, $path, $level + 1));
            }
            else {
                $res[] = [
                    'control' => str_replace('/', '\\', str_replace('.php', '', $path)),
                    'path' => $path,
                    'level' => $level
                ];
            }
        }
        return $res;
    }

    public function scanAction() {
        $res = [];
        $except = ['__construct'];
        $controls = $this->scanControl(__SHARED.'Controller');
        foreach ($controls as $control) {
            $item = [
                'control' => $control['control'],
                // 'path' => $control['path'],
                'level' => $control['level']
            ];
            $class = '\\Controller\\'.$control['control'];
            $app = new $class();
            foreach (get_class_methods($app) as $action) {
                if (!in_array($action, $except) && is_callable([$app, $action])) {
                    $item['action'] = $action;
                    $res[] = $item;

                    // check exist, add, update to database
                }
            }
        }
        var_dump($res);
    }
}