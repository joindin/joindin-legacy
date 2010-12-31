<?php

// autoloader
function __autoload($classname) {
	if(false !== strpos($classname, '.')) {
		// this was a filename, don't bother
		exit;
	}

	if(preg_match('/[a-zA-Z]+Controller$/',$classname)) {
		include('../controllers/' . $classname . '.php');
		return true;
	} elseif(preg_match('/[a-zA-Z]+Model$/',$classname)) {
		include('../models/' . $classname . '.php');
		return true;
	}
}

// Add exception handler
function handle_exception($e) {
	echo "BADNESS";
	var_dump($e);
	error_log('Exception Handled: ' . $e->getMessage());
}
set_exception_handler('handle_exception');

// config setup
define('BASEPATH', '.');
include('../database.php');
$ji_db = new PDO('mysql:host=' . $db['default']['hostname'] . 
    ';dbname=' . $db['default']['database'],
    $db['default']['username'],
    $db['default']['password']);

// collect URL and headers
$request = new Stdclass();
$request->verb = $_SERVER['REQUEST_METHOD'];
$request->url_elements = explode('/',$_SERVER['PATH_INFO']);
parse_str($_SERVER['QUERY_STRING'], &$parameters);
$request->parameters = $parameters;
$request->accept = $_SERVER['HTTP_ACCEPT'];

// Authenticate: if this is a valid user, add $request->user_id 

// Route: call the handle() method of the class with the first URL element
// (ignoring empty [0] element from leading slash)
if(!empty($request->url_elements[1])) {
	$class = ucfirst($request->url_elements[1]) . 'Controller';
	$handler = new $class();
	$handler->handle($request, $ji_db); // the DB is set by the database config


	// Handle output
	exit;
} else {
	throw new Exception('Documentation should appear here');
}

