<?php
include '../inc/Autoloader.php';
include '../inc/Request.php';
include '../inc/Timezone.php';

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

// Set the correct charset for this connection
$ji_db->query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
$ji_db->query('SET CHARACTER SET utf8');


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
            // JSONP?
            $callback = filter_var($request->getParameter('callback'), FILTER_SANITIZE_STRING);
            if($callback) {
                $request->view = new JsonPView($callback);
            } else {
                $request->view = new JsonView();
            }
            break;
}

$version = $request->getUrlElement(1);
switch ($version) {
    case 'v2':
        // default routing for version 2
        $return_data = routeV2($request, $ji_db);
        break;
    
    case '':
        // version parameter not specified routes to default controller
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
    // check if this is an oauth request and if so, who are we?
    if(array_key_exists('Authorization', apache_request_headers())
        && ($request->url_elements[2] != 'oauth')) {
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


