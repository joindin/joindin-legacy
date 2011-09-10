<?php

/**
 * API v2 test suite bootstrap file
 *
 * PHP version 5
 *
 * @category Bootstrap
 * @package  APIv2_Tests
 * @author   Rob Allen <rob@akrabat.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */
define('BASEPATH', __DIR__ . '/../../../api-v2');

// Register the api-v2 autoloader
require BASEPATH . '/inc/Autoloader.php';


define('TEST_DB_KEY', 'apiv2test');

/**
 * get the db adapter
 *
 * @global array $db
 * @staticvar PDO $dbAdapter
 * 
 * @return PDO 
 */
function getDbAdapater()
{
    global $db;
    static $dbAdapter;

    if (!$dbAdapter) {
        include_once BASEPATH . '/database.php';

        $dbAdapter = new PDO(
            'mysql:host=' . $db[TEST_DB_KEY]['hostname'] .
            ';dbname=' . $db[TEST_DB_KEY]['database'],
            $db[TEST_DB_KEY]['username'],
            $db[TEST_DB_KEY]['password']
        );
    }
    return $dbAdapter;
}

