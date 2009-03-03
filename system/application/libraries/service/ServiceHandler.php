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
     * The output type to use when returning data from this handler.
     * @var string
     */
    protected $_outputType = 'xml';
    
    /**
     * The HTTP status code to send when returning data from this handler.
     * @var int
     */
    protected $_statusCode = 200;

    protected $_xmlData = null;
    
    protected $_queryString = '';
    
    /**
     * Handler constructor
     * @param SimpleXmlElement $xmlData
     * @param string $queryString
     */
    public function __construct($xmlData, $queryString)
    {
        $this->_xmlData = $xmlData;
        $this->_querystring = $queryString;
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
     * Returns the status code for this handler.
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
     * Sets the xml data for the handler to work with.
     * This data can also include the data necessary for authentication.
     * @param SimpleXmlElement $xmlData
     */
    public function setXmlData($xmlData)
    {
        $this->_xmlData = $xmlData;
    }
    
    /**
     * Sets the query string for the handler to work with.
     * @param string $queryString
     */
    public function setQueryString($queryString)
    {
        $this->_queryString = $queryString;
    }
    
}
