<?php

define('__ROOT', realpath(__DIR__) . '/');
define('__APP', __ROOT. 'app/');
define('__SYS', __ROOT. 'system/');

include(__SYS . 'Singleton.php');
include(__SYS . 'Reg.php');
include(__SYS . 'Config.php');
include(__SYS . 'Request.php');
include(__SYS . 'View.php');
include(__SYS . 'Route.php');

spl_autoload_register(function($name) {
	$_ = explode('\\', $name);
	$type = lcfirst($_[0]);
	unset($_[0]);
	$file = __APP . $type.'/' .implode('/', $_). '.php';
	if (isset($_[1]) && is_file($file)) {
		include($file);
	}
});

include(__APP . 'boot.php');
$route = new \Route();
include(__APP . 'route.php');
$route->match();

// autoload
// load config

// css, js dùng postCss, gulp hoặc kiểu vậy

// phân quyền, validate, upload
