<?php 
/**
 * Joindin webservice for site status
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed'); 
}

/**
 * Joindin webservice for site status
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * 
 */
class Status extends BaseWsRequest
{
    public $CI  = null;
    public $xml = null;
    
    /**
     * Instantiates the service for getting site status
     *
     * @param string $xml The XML sent into the request
     *
     * @return null
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance();
        $this->xml = $xml;
    }

    /**
     * Public function. 
     *
     * @return true
     */
    public function checkSecurity() 
    {
        //public function!
        return true;
    }

    /**
     * Retrieves the date from the server and if provided, returns
     * a test string as well.
     *
     * @return array
     */
    public function run() 
    {
        $arr = array(
            'data'=>array(
                'items'=>array(
                    'dt'=>date('r', time())
                )
            ),
            'output'=>'json'
        );
        //
        // If they give us a test string, echo it back to them
        if (isset($this->xml->action->test_string)) {
            // cast this to string, otherwise we get the whole SimpleXMLElement
            // object in there
            $arr['data']['items']['test_string'] 
                = (string) $this->xml->action->test_string;
        }
        return $arr;
    }
    
}
