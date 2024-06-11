<?php

define('__ROOT', realpath(__DIR__) . '/');
define('__APP', __ROOT. 'app/');
define('__SYS', __ROOT. 'system/');

include(__SYS . 'Reg.php');
include(__SYS . 'Config.php');
include(__SYS . 'Request.php');
include(__SYS . 'View.php');
include(__SYS . 'Route.php');

spl_autoload_register(function($name) {
	$_ = explode('\\', $name);
	if (isset($_[1]) && ($file = __APP.implode('/', $_).'.php') && is_file($file)) {
		include($file);
	}
	else {
		include(__SYS. $name.'.php');
	}
});

include(__APP . 'boot.php');
$route = new \Route();
include(__APP . 'route.php');
$route->match();


// cache in repo sang một bảng tạm thời, hợp lý không? cache các mối quan hệ liên kết đén bảng khác
