<?php
/**
 * Class ServiceResponseJson
 */

/**
 * Converts to a JSON encoded string that can be used in 
 * a service response.
 * 
 * @author Mattijs Hoitink <mattijs@ibuildings.nl>
 */
class ServiceResponseJson
{

    /**
     * The data to be converted to JSON
     * @var mixed
     */
    protected $_data = null;
    
    public function __construct($data)
    {
        $this->_data = $data;
    }
    
    /**
     * Returns a response in JSON format
     * @return string
     */
    public function getResponse()
    {
        return json_encode($this->_data);
    }
}