<?php
/**
 * Class ServiceResponseJson
 */

/**
 * Converts to a JSON encoded string that can be used in 
 * a service response.
 * 
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class ServiceResponseJson
{

    /**
     * The data to be converted to JSON
     * @var mixed
     */
    protected $_data = array();
    
    public function __construct()
    {}
    
    /**
     * Adds data to the response data
     * @param mixed $value
     * @param string $key
     */
    public function addData($value, $key = '')
    {
        if(!empty($key)) {
            $this->_data[$key] = $value;
        } else {
            $this->_data[] = $value;
        }
    }
    
    public function addArray($array, $key = '')
    {
        $this->addData($array, $key);
    }
    
    public function addString($string, $key = '') 
    {
        $this->addData($string, $key);
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
