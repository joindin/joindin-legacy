<?php
include '../inc/Request.php';

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
$request = new Request();

// set some default parameters
$request->parameters['resultsperpage'] = $request->getParameter('resultsperpage', 20);
$request->parameters['start'] = $request->getParameter('start', 0);


// Which content type to return? Parameter takes precedence over accept headers 
// with final fall back to json 
$format_choices = array('application/json', 'text/html');
$header_format = $request->preferredContentTypeOutOf($format_choices);
$format = $request->getParameter('format', $header_format);

switch ($format) {
        case 'text/html':
        case 'html':
            $request->view = new HtmlView();
            break;
        
        case 'application/json':
        case 'json':
        default:
            $request->view = new JsonView();
            break;
}

$version = $request->getUrlElement(1);
switch ($version) {
    case 'v2':
        // default routing
        $return_data = routeV2($request, $ji_db);
        break;
    
    case '':
        // paramerter not specified
        $defaultController = new DefaultController();
        $return_data = $defaultController->handle($request, $ji_db);
        break;
    
    default:
        // unexpected version
        throw new Exception('API version must be specified', 404);
        break;
}

// Handle output
// TODO sort out headers, caching, etc
$request->view->render($return_data);
exit;

/**
 *
 * @param Request $request
 * @param PDO $ji_db
 * @return array
 */
function routeV2($request, $ji_db)
{
    $return_data = false;
    if(isset($request->parameters['oauth_version']) && ($request->url_elements[2] != 'oauth')) {
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
    
    return $return_data;
}


