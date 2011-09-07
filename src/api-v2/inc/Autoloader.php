<?php
/**
 * Autoloader
 *
 * PHP version 5
 *
 * @category Inc
 * @package  APIv2_Tests
 * @author   Rob Allen <rob@akrabat.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */

spl_autoload_register('apiv2Autoload');

/**
 * Autoloader
 * 
 * @param string $classname name of class to load
 * 
 * @return boolean
 */
function apiv2Autoload($classname)
{
    if (false !== strpos($classname, '.')) {
        // this was a filename, don't bother
        exit;
    }

    if (preg_match('/[a-zA-Z]+Controller$/', $classname)) {
        include __DIR__ . '/../controllers/' . $classname . '.php';
        return true;
    } elseif (preg_match('/[a-zA-Z]+Mapper$/', $classname)) {
        include __DIR__ . '/../models/' . $classname . '.php';
        return true;
    } elseif (preg_match('/[a-zA-Z]+Model$/', $classname)) {
        include __DIR__ . '/../models/' . $classname . '.php';
        return true;
    } elseif (preg_match('/[a-zA-Z]+View$/', $classname)) {
        include __DIR__ . '/../views/' . $classname . '.php';
        return true;
    }
}
