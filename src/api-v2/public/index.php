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

// identify our user if applicable
$headers = apache_request_headers();
if(isset($headers['Authorization'])) {
    $request->identifyUser($ji_db, $headers['Authorization']);
}

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
    case 'v2.1':
        // default routing for version 2.1
        $request->version = "v2.1";
        $return_data = routeV2($request, $ji_db);
        break;
    
    case '':
        // current newest version
        $request->version = "v2.1";
        // version parameter not specified routes to default controller
        $defaultController = new DefaultController();
        $return_data = $defaultController->handle($request, $ji_db);
        break;

    case 'v2':
        // old versions
        throw new Exception('This API version is no longer supported.  Please use v2.1');
        break;
    
    default:
        // unexpected version
        throw new Exception('API version must be specified', 404);
        break;
}
if(isset($request->user_id)) {
    $return_data['meta']['user_uri'] = $request->base . '/' . $request->version . '/users/' . $request->user_id;
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


