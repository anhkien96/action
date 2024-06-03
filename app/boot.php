<?php

if (\Config::get('app.debug')) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
}

$timezone = \Config::get('app.timezone');
if ($timezone) {
	date_default_timezone_set($timezone);
}

\Reg::map('db', '\Lib\DB');
\Reg::map('request', '\Request');
\Reg::map('view', '\View');
\Reg::map('validator', '\Lib\Validator');
\Reg::map('validate', '\Lib\Validate');
\Reg::map('user', '\Model\User\PublicUser');