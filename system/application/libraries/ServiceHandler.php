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

    /**
     * Checks if the request is authorized to access the api call
     * @param mixed $authData
     * @return boolean
     */
    public abstract function isAuthorized($authData);
    
    /**
     * Called for hadnling a service request
     * @param mixed $actionData
     */
    public abstract function handle($actionData);
    
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
    
}
