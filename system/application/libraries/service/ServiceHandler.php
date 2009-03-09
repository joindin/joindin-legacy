<?php
/**
 * Class ServiceHandler
 */


/**
 * Handles a service request and outputs the 
 * response to the user.
 * 
 * @author Mattijs Hoitink <mattijs@ibuildings.nl>
 *
 */
abstract class ServiceHandler
{
    /**
     * Reference to the manager this ServiceHandler was spawned from.
     * @var ServiceManager
     */
    protected $_manager = null;
    
    /**
     * The output type to use when returning data from this handler.
     * @var string
     */
    protected $_outputType = 'xml';
    
    /**
     * The HTTP status code to send when returning data from this handler.
     * @var int
     */
    protected $_statusCode = 200;

    /**
     * XML data passed into this handler
     * @var SimpleXmlElement
     */
    protected $_xmlData = null;
    
    /**
     * Query string passed into this handler
     * @var string
     */
    protected $_queryString = '';
    
    /**
     * Handler constructor
     * @param SimpleXmlElement $xmlData
     * @param string $queryString
     */
    public function __construct($xmlData, $queryString = '')
    {
        $this->_xmlData = $xmlData;
        $this->_queryString = $queryString;
    }
    
    /**
     * Checks if the request is authorized to access the api call
     * @param mixed $authData
     * @return boolean
     */
    public abstract function isAuthorizedRequest();
    
    /**
     * Called for hadnling a service request
     * @param mixed $actionData
     */
    public abstract function handle();
    
    /**
     * Sets the output type for this handler
     * @param string $type
     */
    public function setOutputType($type)
    {
        $this->_outputType = $type;
    }
    
    /**
     * Returns the status code for this handler. If an output type needs to be 
     * forced, override this method in the handler implementation.
     * @return string
     */
    public function getOutputType()
    {
        return $this->_outputType;
    }
    
    /**
     * Returns the HTTP status code for this handler.
     * @return int
     */
    public function getStatusCode()
    {
        return $this->_statusCode;    
    }
    
    /**
     * Sets the manager this handler was spawned from.
     * @param ServiceManager $manager
     */
    public function setManager(ServiceManager $manager)
    {
        $this->_manager = $manager;
    }
    
    protected function _sendError($reason, $statusCode = 404)
    {
        $this->_manager->sendError($reason, $statusCode, $this->getOutputType());
    }
    
    protected function _sendRedirect($url, $statusCode = 302)
    {
        $this->_manager->sendRedirect($url, $this->getOutputType(), $statusCode);
    }
    
}
