<?php

if (\Config::get('app.debug')) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
}

$timezone = \Config::get('app.timezone');
if ($timezone) {
	date_default_timezone_set($timezone);
}

\Reg::set('view', '\View');
\Reg::set('request', '\Request');
\Reg::set('db', '\Lib\DB');
\Reg::set('validator', '\Lib\Validator');
\Reg::set('validate', '\Lib\Validate');