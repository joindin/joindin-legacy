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
     * Instantiates the web service which toggles a user's status
     *
     * @param string $xml XML to set
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Checks that the current user is a site admin
     *
     * @param string $xml XML to check
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
     * Runs the webservice to toggle a user's status
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->model('user_model','um');
        $uid = $this->xml->action->uid;
        
        $this->CI->um->toggleUserStatus($uid);
        return array('output'=>'json','items'=>array('msg'=>'Success'));
    }
}

