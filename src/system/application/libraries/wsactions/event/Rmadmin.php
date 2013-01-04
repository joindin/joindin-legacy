<?php 
/**
 * Joindin webservice for removing an admin from an event
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
 * Joindin webservice for removing an admin from an event
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Rmadmin extends BaseWsRequest
{
    public $CI  = null;
    public $xml = null;

    /**
     * Instantiates web service to remove the event admin
     *
     * @param string $xml XML sent to service
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Ensures that the caller is a site admin or event admin
     *
     * @param string $xml XML sent to service
     *
     * @return boolean
     */
    public function checkSecurity($xml) 
    {
        $this->CI->load->model('user_model', 'um');
        $this->CI->load->model('user_admin_model', 'uam');
        
        // Check for a valid logged in user, either via the auth 
        // or sessions
        if ($this->CI->um->isAuth() || $this->isValidLogin($xml)) {
            // They either need to be an admin of the event or a 
            // site admin
            
            $user = false;
            if ($this->CI->um->isAuth()) {
                $user = $this->CI->session->userdata('username');
            } elseif (!$this->CI->um->isAuth()) {
                $user = $xml->auth->user;
            }

            $udata = $this->CI->um->getUserByUsername((string)$user);
            if (!empty($udata)) { 
                $user_id  = $udata[0]->ID;
                $username = $udata[0]->username;
            } else { 
                return false; 
            }

            $eid   = (int)$xml->action->eid;
            $rtype = 'event';
            
            // Event ID must be an integer
            if (!is_int($eid)) {
                return array(
                    'output'=>'json',
                    'data'=>array(
                        'items'=>array(
                            'msg'=>'Invalid Event ID!')
                        )
                    );
            }
            $is_evt_admin  = $this->CI->uam->hasPerm($user_id, $eid, $rtype);
            $is_site_admin = $this->CI->um->isSiteAdmin($username);
            
            if ($is_site_admin || $is_evt_admin) {
                return true;
            } else {
                return false;
            }
            
        } else {
            return false;
        }
    }

    /**
     * Removes the user's event admin permissions
     *
     * @return array
     */
    public function run() 
    {
        if (!isset($this->xml->action->eid) 
            || !isset($this->xml->action->username)
        ) {
            return array(
                'output'=>'json',
                'data'=>array(
                    'items'=>array(
                        'msg'=>'Required fields missing!')
                    )
                );
        }
        $this->CI->load->model('user_admin_model', 'uam');
        $this->CI->load->model('user_model', 'um');
        
        $user = $this->xml->action->username;
        $eid  = (int)$this->xml->action->eid;
        $type = 'event';
        
        // Event ID must be an integer
        if (!is_int($eid)) {
            return array(
                'output'=>'json',
                'data'=>array(
                    'items'=>array(
                        'msg'=>'Invalid Event ID!')
                    )
                );
        }

        if (!is_int($user)) {
            $udata = $this->CI->um->getUserByUsername((string)$user);
            if (!empty($udata)) {
                $user = $udata[0]->ID;
            } else { 
                return false;
            }
        }
        
        $this->CI->uam->removeRidPerm($user, $eid, $type);
        return array(
            'output'=>'json',
            'data'=>array(
                'items'=>array(
                    'msg'=>'Success')
                )
            );
    }    
}

