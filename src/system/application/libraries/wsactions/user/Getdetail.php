<?php 
/**
 * Joindin webservice for retrieving details for a user
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
 * Joindin webservice for retrieving details for a user
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Getdetail extends BaseWsRequest
{
    
    public $CI  = null;
    public $xml = null;
    
    /**
     * Builds the webservice instance
     *
     * @param string $xml XML to set
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Does nothing but return true
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
     * Runs the webservice action to get the details for a user
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->model('user_model');
        $this->CI->load->library('wsvalidate');

        // uid must be numeric user id 
        $uid = $this->xml->action->uid;

        $rules = array(
            'uid'    =>'required'
        );

        $ret = $this->CI->wsvalidate->validate($rules, $this->xml->action);

        if (!$ret) {
            $ret = $this->CI->user_model->getUserDetail(sprintf('%s', $uid));

            return array('output'=>'json','data'=>array('items'=>$ret));
        } else {
            return array(
                'output'=>'json',
                'data'=>array(
                    'items'=>array(
                        'msg'=>'Required field uid missing!')
                    )
                );
        }
    }
    
}

