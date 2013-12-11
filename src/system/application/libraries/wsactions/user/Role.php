<?php 
/**
 * Joindin webservice for modifying a user's role
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
 * Joindin webservice for modifying a user's role
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Role extends BaseWsRequest
{
    public $CI  = null;
    public $xml = null;
    
    /**
     * Instantiates the web service that changes a user's role
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
     * @param string $xml Not used
     *
     * @return boolean
     */
    public function checkSecurity($xml) 
    {
        $this->CI->load->model('user_model');
        
        // Check for a valid login
        //if ($this->isValidLogin($xml) || $this->CI->user_model->isAuth()) {
        if ($this->CI->user_model->isAuth()) {
            // Now check to see if they're a site admin
            $user = $this->CI->session->userdata('username');
            if (!$this->CI->user_model->isSiteAdmin($user)) {
                return false;
            } else { 
                return true; 
            }
        } else {
            return false;
        }
    }

    /**
     * Runs the webservice to modify a user's role
     *
     * @return param
     */
    public function run() 
    {
        $this->CI->load->model('user_admin_model', 'uam');
        $type = $this->xml->action->type;
        
        $result = array();
        if ($type == 'remove') {
            $aid           = $this->xml->action->aid;
            $result['aid'] = (int)$aid;
            $this->CI->uam->removePerm($aid);
        } elseif ($type == 'addevent') {
            $uid           = $this->xml->action->uid;
            $rid           = $this->xml->action->rid;
            $result['uid'] = (int)$uid;
            $result['rid'] = (int)$rid;
            $this->CI->uam->addPerm($uid, $rid, 'event');
        } elseif ($type == 'addtalk') {
            $uid           = $this->xml->action->uid;
            $rid           = $this->xml->action->rid;
            $result['uid'] = (int)$uid;
            $result['rid'] = (int)$rid;
            $this->CI->uam->addPerm($uid, $rid, 'talk');
        }

        $result['msg'] = 'Success';
        return array('output' => 'json', 'items' => $result);
    }
    
}
