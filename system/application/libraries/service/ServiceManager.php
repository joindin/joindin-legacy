<?php
/**
 * Class ServiceManager
 */

/** ServiceResponseXml */
require_once BASEPATH . 'application/libraries/service/ServiceResponseXml.php';

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
class ServiceManager 
{
    protected $_ci = null;
    
    /**
     * Supported output types and their content type
     * @var array
     */
    protected $_contentTypes = array (
        'plain' => 'text',
    	'xml' => 'application/xml',
        'json' => 'application/javascript',
    );
    
    /**
     * Status code headers
     * @var array
     */
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
    public function dispatch($service, $rawData)
    {
        // Parse the incomming data
        $data = $this->_parseData($rawData);
        
        $xml = $data['xml'];
        $action = ucfirst($xml->action['type']);
        
	    // Find the handler for the request
	    $handlerFile = dirname(__FILE__) . '/handlers/' . $service . '/' . $action . '.php';
	    if(!is_file($handlerFile)) {
	        // return invalid request
	        $this->_sendError('Bad Request', 400);
	    }
	    require_once($handlerFile);
	    
	    if(!class_exists($action)) {
	        // return invalid request
	        $this->_sendResponse('Not Found', 404);
	    }
	    
	    // Create a new instance of the handler
	    $handler = new $action($xml, $data['query_string']);
	    
        // Check authorization
        if(!$handler->isAuthorizedRequest()) {
            $this->_sendError('Unauthorized', 401);
        }

	    // Handle the request
	    try {
    	    $handlerResponse = $handler->handle();
    	} catch(Exception $e) {
	        $this->_sendError('Internal Server Error', 500);
    	}
	    
	    // Send the response
	    $outputType = $handler->getOutputType();
	    $statusCode = $handler->getStatusCode();
	    $this->_sendResponse($handlerResponse, $outputType, $statusCode);
    }
    
    /**
     * Parses the raw request data
     * @param array $rawData
     * @return mixed
     */
    protected function _parseData($rawData)
    {
		$xml = null;
        try {
            $xml = @simplexml_load_string($rawData['xml']);
        } catch(Exception $e) {
            // Parsing failed, output error
            $this->_sendResponse('Bad Request', 400);
        }
        
        if(!$xml) {
            return $this->_sendResponse('Bad Request', 400);
        }
        
        return array('xml' => $xml, 'query_string' => $_SERVER['QUERY_STRING']);
    }
    
    /**
     * Sends an error message back to the client.
     * @param string $reason
     * @param int $statuscode
     */ 
    protected function _sendError($reason, $statusCode)
    {
        $xmlReponse = new ServiceResponseXml();
        $xmlReponse->addString($reason, 'error');
        $this->_sendResponse($xmlReponse->getResponse(), 'xml', $statusCode);
    }
    
    /**
     * Sends the reponse back to the client
     * @param mixed $data
     * @param string $outputType
     * @param int $statusCode
     */
    protected function _sendResponse($data, $outputType = 'xml', $statusCode = 200)
    {
        // Get the content type
        $contentType = (array_key_exists($outputType, $this->_contentTypes)) ? $this->_contentTypes[$outputType] : $this->_contentTypes['xml'];
        
        // Send the reponse to the client
        header('HTTP/1.1 ' . $statusCode . ' ' . $this->_statusCodes[$statusCode]);
        header('Content-Type: ' . $contentType);
        echo trim($data);
        exit;
    }
    
}
