<?php 
/**
 * Joindin webservice for toggling a user's status
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
 * Joindin webservice for toggling a user's status
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Status
{
    
    public $CI  = null;
    public $xml = null;
    
    /**
     * Builds the Status webservice object
     *
     * @param string $xml XML to process
     */
    public function Status($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Determines if the user is a site admin in order to be able to run this 
     * web service
     *
     * @param string $xml XML to process
     *
     * @return boolean
     */
    public function checkSecurity($xml) 
    {
        if ($this->isValidLogin($xml)) {
            if ($this->CI->user_model->isSiteAdmin((string)$xml->auth->user)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Runs the webservice
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->model('user_model', 'um');
        $uid = $this->xml->action->uid;
        
        $this->CI->um->toggleUserStatus($uid);
        return array('output' => 'json', 'items' => array('msg' => 'Success'));
    }
}

