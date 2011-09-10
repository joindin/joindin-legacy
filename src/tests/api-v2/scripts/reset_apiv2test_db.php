<?php
/**
 * Reset the APIv2 test database by lookingin database.php for the credentials
 *
 * PHP version 5
 *
 * @category Bootstrap
 * @package  APIv2_Tests
 * @author   Rob Allen <rob@akrabat.com>
 * @license  BSD see doc/LICENSE
 * @link     http://github.com/joindin/joind.in
 */

// Register the api-v2 autoloader
require __DIR__ . '/bootstrap/bootstrap.php';

resetDatabase();

/**
 * wrapper function abour reset_db.sh that picks up the database information
 * from database.php
 *
 * @global array $db
 * 
 * @return void 
 */
function resetDatabase()
{
    global $db;
    include_once BASEPATH . '/database.php';

    echo "Setting up database\n";

    $cmd = __DIR__ . '/reset_db.sh';
    $dbDir = realpath(__DIR__ . '/../../../doc/db');
    $dbName = $db[TEST_DB_KEY]['database'];
    $dbUser = $db[TEST_DB_KEY]['username'];
    $dbPassword = $db[TEST_DB_KEY]['password'];
    $saveDumpFile = 1;

    system("$cmd $dbDir $dbName $dbUser $dbPassword $saveDumpFile", $returnValue);
    
    exit($returnValue);
}
