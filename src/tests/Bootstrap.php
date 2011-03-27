<?php
// Set the default timezone !!!
date_default_timezone_set('Europe/Brussels');

// We wanna catch all errors en strict warnings
error_reporting(E_ALL|E_STRICT);

// set our app paths and environments
define('ROOTPATH', realpath(dirname(__FILE__) . '/../../'));
define('APPPATH', ROOTPATH . '/src/system/application/');
define('LIBPATH', ROOTPATH . '/src/system/libraries');
define('CIPATH', ROOTPATH . '/src/system/codeigniter');
define('TESTPATH', ROOTPATH . '/src/tests');

// defining CI constants
define('BASEPATH', ROOTPATH . '/src/system/');
define('EXT', '.php');

// Include path
$paths = array (
    APPPATH . 'controllers',
    APPPATH . 'config',
    APPPATH . 'models',
    APPPATH . 'libraries',
    CIPATH,
    LIBPATH,
    get_include_path(),
);
set_include_path(implode(PATH_SEPARATOR, $paths));

// Loading the CodeIgniter classes
if (-1 === version_compare(phpversion(), 5.0)) {
    require_once CIPATH . '/Base4.php';
} else {
    require_once CIPATH . '/Base5.php';
}
require_once CIPATH . '/CodeIgniter.php';
require_once CIPATH . '/Common.php';
require_once CIPATH . '/Compat.php';

// Registering an auto loader
spl_autoload_register('_joindinLoader');

function _joindinLoader($class)
{
    $paths = explode(PATH_SEPARATOR, get_include_path());
    $filename = '/' . $class . '.php';
    foreach ($paths as $path) {
        $classFile = realpath($path . $filename);
        if (file_exists($classFile)) {
            require_once $classFile;
            return true;
        }
    }
    return false;
}

