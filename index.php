<?php

define('__ROOT', realpath(__DIR__) . '/');
define('__SHARED', __ROOT. 'shared/');
define('__SITE', __ROOT. 'site/');
define('__SYS', __ROOT. 'system/');

include(__SYS . 'Reg.php');
include(__SYS . 'Config.php');
include(__SYS . 'Request.php');
include(__SYS . 'Response.php');
include(__SYS . 'View.php');
include(__SYS . 'Route.php');

// spl_autoload_register(function($name) {
// 	$_ = explode('\\', $name);
// 	if (isset($_[1]) && ($file = __SHARED.implode('/', $_).'.php') && is_file($file)) {
// 		include($file);
// 	}
// 	else {
// 		include(__SYS. $name.'.php');
// 	}
// });

spl_autoload_register(function($name) {
	$_ = explode('\\', $name);
	if (isset($_[1])) {
		if ($_[0] == 'site') {
			$file = __ROOT.implode('/', $_).'.php';
		}
		else {
			$file = __SHARED.implode('/', $_).'.php';
		}
		if (is_file($file)) {
			include($file);
		}
	}
	else {
		include(__SYS. $name.'.php');
	}
});

include(__SHARED . 'boot.php');
$route = \Reg::get('route');
include(__SHARED . 'route.php');
$route->match();

// cache in repo sang một bảng tạm thời, hợp lý không? cache các mối quan hệ liên kết đén bảng khác
// chức năng update hệ thống từ trung tâm giống kiểu wordpress

// repo update, check trường update -> tác động cái gì, kiểu onchange giống Odoo
// Odoo nó hỗ trợ onchange ở model để reactive trên giao diện, nên được như Odoo thì tốt, không được cũng tạm dùng được rồi

// cần thêm lớp Event, Listen không?
// học Symfony, đỉnh cao hơn Laravel nếu hiểu tận gốc, còn triển khai Mautic,...

// PHP quản trị, web, fontend cho phép sửa nhanh.
// Hệ thống lập lịch đa luồng có thể dùng Python, haha, PHP gọi API sang Python, ...
