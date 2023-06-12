<?php

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 1);
date_default_timezone_set('CET');

// defines the web root
define('WEB_ROOT', substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], '/index.php')));
// defindes the path to the files
define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
// defines the cms path
define('CMS_PATH', ROOT_PATH . '/lib/base/');
// defines the vendor path
define('VENDOR_PATH', ROOT_PATH . '/lib/vendor/');

// Set a session variable
$_SESSION['db_type'] = 'json';
// starts the session
session_start();

// This path should point to Composer's autoloader
//require VENDOR_PATH.'autoload.php';

// includes the system routes. Define your own routes in this file
include(ROOT_PATH . '/config/routes.php');

/**
 * Standard framework autoloader
 * @param string $className
 */
function autoloader($className) {
	// controller autoloading
	if (strlen($className) > 10 && substr($className, -10) == 'Controller') {
		if (file_exists(ROOT_PATH . '/app/controllers/' . $className . '.php') == 1) {
			require_once ROOT_PATH . '/app/controllers/' . $className . '.php';
		}
	}
	else {
		if (file_exists(CMS_PATH . $className . '.php')) {
			require_once CMS_PATH . $className . '.php';
		}
		else if (file_exists(ROOT_PATH . '/lib/' . $className . '.php')) {
			require_once ROOT_PATH . '/lib/' . $className . '.php';
		}
		else {
			require_once ROOT_PATH . '/app/models/'.$className.'.php';
		}
	}
}

// activates the autoloader
spl_autoload_register('autoloader');

$router = new Router();
$router->execute($routes);
