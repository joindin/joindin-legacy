<?php 
/**
 * Joindin webservice for validating users
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   WebServices
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed'); 
}

/**
 * Joindin webservice for validating users
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   WebServices
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Validate extends BaseWsRequest
{
    
    public $CI  = null;
    public $xml = null;
    
    /**
     * Constructs the Validate object and sets the xml value provided. 
     * Retrieves and sets CodeIgniter instance on the object.
     *
     * @param string $xml XML to set
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
    * Only site admins can use this functionality
    *
    * Returns true. Ignores the parameter, does nothing but
    * returns true.
    *
    * @param mixed $xml Not used
    *
    * @return true
    */
    public function checkSecurity($xml) 
    {
        //public function!
        return true;
    }
    
    /**
     * Checks if the user of the webservice is a valid user
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->model('user_model');
        
        // check for a valid login
        $ret = array('msg'=>'Invalid user');;
        if (isset($this->xml->action->uid) 
            && isset($this->xml->action->pass)
        ) {
            // check to see if they're a valid user
            if ($this->CI->user_model->validate(
                (string)$this->xml->action->uid,
                (string)$this->xml->action->pass,
                true
            )) {
                $ret = array('msg'=>'success');;
            }
        }
        return array('output' => 'json', 'data' => array('items' => $ret));
    }
    
}

