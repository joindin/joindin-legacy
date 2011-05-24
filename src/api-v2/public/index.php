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
	} elseif(preg_match('/[a-zA-Z]+View$/',$classname)) {
		include('../views/' . $classname . '.php');
		return true;
	}
}

// Add exception handler
function handle_exception($e) {
    // pull the correct format before we bail
    global $request;
    header("Status: " . $e->getCode(), false, $e->getCode());
	$request->view->render(array($e->getMessage()));
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
if(isset($_SERVER['PATH_INFO'])) {
    $request->url_elements = explode('/',$_SERVER['PATH_INFO']);
}
parse_str($_SERVER['QUERY_STRING'], &$parameters);
$request->accept = explode(',', $_SERVER['HTTP_ACCEPT']);
$request->host = $_SERVER['HTTP_HOST'];
$request->parameters = $parameters;

// set some default parameters
$request->parameters['resultsperpage'] = isset($request->parameters['resultsperpage']) 
    ? $request->parameters['resultsperpage'] : 20;
$request->parameters['start'] = isset($request->parameters['start']) 
    ? $request->parameters['start'] : 0;

// Input Handling: parameter takes precedence
if(isset($request->parameters['format'])) {
    switch($request->parameters['format']) {
        case 'html':
            $request->view = new HtmlView();
            break;
        case 'json':
            $request->view = new JsonView();
            break;
        default:
            // use the accept headers instead
            break;
    }
}
// Input Handling: then check the accept headers, fall back to json 
if(!isset($request->view)) {
    // TODO handle other items in the accept header
    switch($request->accept[0]) {
        case 'text/html':
            $request->view = new HtmlView();
            break;
        default:
            $request->view = new JsonView();
            break;
    }
}

if(isset($request->url_elements[1])) {
    // check API version
    switch($request->url_elements[1]) {
        case 'v2':
                    // default routing
                    break;
        default:
                    throw new Exception('API version must be specified', 404);
                    break;
    }

    if(isset($parameters['oauth_version']) && ($request->url_elements[2] != 'oauth')) {
        $oauth_model = new OAuthModel();
        $oauth_model->in_flight = true;
        $oauth_model->setUpOAuthAndDb($ji_db);
        $request->user_id = $oauth_model->user_id;
    }

    // Route: call the handle() method of the class with the first URL element
    if(isset($request->url_elements[2])) {
        $class = ucfirst($request->url_elements[2]) . 'Controller';
        if(class_exists($class)) {
            $handler = new $class();
            $return_data = $handler->handle($request, $ji_db); // the DB is set by the database config
        } else {
            throw new Exception('Unknown controller ' . $request->url_elements[2], 400);
        }
    } else {
        throw new Exception('Request not understood', 404);
    }
} else {
    $defaultController = new DefaultController();
    $return_data = $defaultController->handle($request, $ji_db);
}

// Handle output
// TODO sort out headers, caching, etc
$request->view->render($return_data);

