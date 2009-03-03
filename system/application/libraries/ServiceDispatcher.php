<?php
/**
 * Class ServiceDispatcher
 */


/**
 * Dispatches a service request to the correct 
 * ServiceHandler.
 * 
 * Tries to find the handler and dispatches the request to 
 * the handler
 * 
 * @author Mattijs Hoitink <mattijs@ibuildings.nl>
 *
 */
class ServiceDispatcher 
{
    protected $_ci = null;
    
    /**
     * Supported output types and their template name
     * @var array
     */
    protected $_outputTypes = array(
        'plain' => 'output_plain',
        'xml' => 'output_xml',
        'json' => 'output_json',
    );
    
    protected $_statusCodes = array(
        200 => 'OK',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        503 => 'Service Unavailable',
    );
    
    public function __construct()
    {
        $this->_ci =& CI_Base::get_instance();
    }
    
    /**
     * Recieves a service request and dispatches it to 
     * the correct ServiceHandler
     * 
     * @param string $service
     * @param string $data
     */
    public function dispatch($service, $data)
    {
	    // Parse the xml
        $xml = $this->_parseRequest($data);
        
        if(!$xml) {
            return $this->_sendResponse('Bad Request', 400);
        }
        
        $action = ucfirst($xml->action['type']);
        
	    // Find the handler for the request
	    $handlerFile = $_SERVER['DOCUMENT_ROOT'] . 'system/application/libraries/handlers/' . $service . '/' . $action . '.php';
	    if(!is_file($handlerFile)) {
	        // return invalid request
	        $this->_sendError('Invalid request', '');
	    }
	    require_once($handlerFile);
	    
	    if(!class_exists($action)) {
	        // return invalid request
	        $this->_sendResponse('Not Found', 404);
	    }
	    
	    // Create a new instance of the handler
	    $handler = new $action;
	    
        // Check authorization
        $authData = (isset($xml->auth)) ? $xml->auth : null;
        if(!$handler->isAuthorized($authData)) {
            $this->_sendError('Forbidden', 403);
        }

	    // Handle the request
	    try {
    	    $handlerResponse = $handler->handle($data);
    	} catch(Exception $e) {
	        $this->_sendError('Internal Server Error', 500);
    	}
	    
	    // Send the response
	    $outputType = $handler->getOutputType();
	    $statusCode = $handler->getStatusCode();
	    $this->_sendResponse($handlerResponse, $outputType, $statusCode);
    }
    
    /**
     * Parses the request xml
     * @param string $raw
     * @return mixed
     */
    protected function _parseRequest($raw)
    {
		$xml = null;
        try {
            $xml = @simplexml_load_string($raw);
        } catch(Exception $e) {
            // Parsing failed, output error
            $this->_sendResponse('Invalid request.', 'plain');
        }
        
        return $xml;
    }
    
    /**
     * Sends an error message back to the client.
     * @param string $reason
     * @param int $statuscode
     */ 
    protected function _sendError($reason, $statusCode)
    {
        $data = array('error' => $reason);
        $this->_sendResponse($data, 'xml', $statusCode);
    }
    
    /**
     * Sends the reponse back to the client
     * @param mixed $data
     * @param string $outputType
     * @param int $statusCode
     */
    protected function _sendResponse($data, $outputType = 'xml', $statusCode = 200)
    {
        // Load the view
        $viewFile = (isset($this->_outputTypes[$outputType])) ? $this->_outputTypes[$outputType] : 'output_plain';
        $viewVars = array(
            'data' => $data,
        );
        $response = $this->_ci->load->view('api/' . $viewFile, $viewVars, true);
        
        // Send the reponse to the client
        header('HTTP/1.1 ' . $statusCode . ' ' . $this->_statusCodes[$statusCode]);
        echo $response;
        exit;
    }
    
}
