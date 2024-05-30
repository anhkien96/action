<?php

define('__ROOT', realpath(__DIR__) . '/');

include(__ROOT . 'system/Singleton.php');
include(__ROOT . 'system/Reg.php');
include(__ROOT . 'system/Config.php');
include(__ROOT . 'system/Request.php');
include(__ROOT . 'system/View.php');
include(__ROOT . 'system/Route.php');

spl_autoload_register(function($name) {
	$_ = explode('\\', $name);
	$type = lcfirst($_[0]);
	unset($_[0]);
	$file = __ROOT . $type.'/' .implode('/', $_). '.php';
	if (isset($_[1]) && is_file($file)) {
		include($file);
	}
});

include(__ROOT . 'config/boot.php');

$route = new \Route();
include(__ROOT . 'config/route.php');
$route->match();

// autoload
// load config

// css, js dùng postCss, gulp hoặc kiểu vậy

// phân quyền, validate, upload
