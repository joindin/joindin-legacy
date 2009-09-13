<?php
/**
 * Class ServiceManager
 */

/** ServiceResponseXml */
require_once BASEPATH . 'application/libraries/service/ServiceResponseXml.php';
/** ServiceResponseJson */
require_once BASEPATH . 'application/libraries/service/ServiceResponseJson.php';

/**
 * Dispatches a service request to the correct 
 * ServiceHandler.
 * 
 * Tries to find the handler and dispatches the request to 
 * the handler
 * 
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
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
        300 => '',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        503 => 'Service Unavailable',
    );
    
    /**
     * Registers the requested output type. Defaults to xml. 
     * This value is just kept for reference when the request is aborted 
     * before the handler output type could be fetched.
     * @var string
     */
    protected $_requestedOutputType = 'xml';
    
    
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
	        $this->sendError('Action not found', 400);
	    }
	    require_once($handlerFile);
	    
	    if(!class_exists($action)) {
	        // return invalid request
	        $this->sendError('Not Found', 404);
	    }
	    
	    // Create a new instance of the handler
	    $handler = new $action($xml, $data['query_string']);
	    $handler->setOutputType($this->_requestedOutputType);
	    $handler->setManager($this);
	    
        // Check authorization
        if(!$handler->isAuthorizedRequest()) {
            $this->sendError('Unauthorized', 401);
        }

	    // Handle the request
	    try {
    	    $handlerResponse = $handler->handle();
    	} catch(Exception $e) {
	        $this->sendError('Internal Server Error', 500);
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
            $this->sendError('Malformed Request', 400);
        }
        
        if(!$xml) {
            return $this->sendError('Malformed Request', 400);
        }
        
        // Find the requested output type
        if(isset($xml->action['output']) && !empty($xml->action['type'])) {
            if(in_array(strtolower((string) $xml->action['output']), array_keys($this->_contentTypes))) {
                $this->_requestedOutputType = (string) $xml->action['output'];
            }
        }
        
        return array('xml' => $xml, 'query_string' => $_SERVER['QUERY_STRING']);
    }
    
    /**
     * Sends an error message back to the client. The ouput type can be overridden 
     * by explicitly passing it as the last paramter. If no output type is specified 
     * in the method call the output type from the request is used.
     * @param string $reason
     * @param int $statuscode
     * @param string $outputType
     */ 
    public function sendError($reason, $statusCode = 400, $outputType = '')
    {
        if(empty($outputType)) {
            $outputType = $this->_requestedOutputType;
        }
        
        $responseClass = 'ServiceResponse' . ucfirst(strtolower($outputType));
        
        $reponse = new $responseClass();
        $reponse->addString($reason, 'error');
        
        $this->_sendResponse($reponse->getResponse(), $outputType, $statusCode);
    }
    
    /**
     * Sends a redirect message back to the client
     * @param string $url
     * @param string $outputType
     */
    public function sendRedirect($url, $outputType = '', $statusCode = 302)
    {
        if(empty($outpyType)) {
            $outputType = $this->_requestedOutputType;
        }
        
        $responseClass = 'ServiceResponse' . ucfirst(strtolower($outputType));
        
        $reponse = new $responseClass();
        $reponse->addString($url, 'redirect');
        
        $this->_sendResponse($reponse->getResponse(), $outputType, $statusCode);
    }
    
    /**
     * Sends the reponse back to the client.
     * @param mixed $data
     * @param string $outputType
     * @param int $statusCode
     */
    protected function _sendResponse($data, $outputType = '', $statusCode = 200)
    {
        if(empty($outpyType)) {
            $outpyType = $this->_requestedOutputType;
        }
        
        // Get the content type
        $contentType = (array_key_exists($outputType, $this->_contentTypes)) ? $this->_contentTypes[$outputType] : $this->_contentTypes['xml'];
        
        // Send the reponse to the client
        header('HTTP/1.1 ' . $statusCode . ' ' . $this->_statusCodes[$statusCode]);
        header('Content-Type: ' . $contentType);
        echo trim($data);
        exit;
    }
    
}
