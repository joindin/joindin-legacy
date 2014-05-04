<?php 
/**
 * Joindin webservice for adding an track
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
 * Joindin webservice for adding an track
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Addtrack extends BaseWsRequest
{
    protected $CI  = null;
    protected $xml = null;
    
    /**
     * Instantiates the webservies
     *
     * @param string $xml XML passed to webservice
     *
     * @return void
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Checks if the user is an admin and able to run the service
     *
     * @param string $xml XML sent to webservice 
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
                $user = (string)$xml->auth->user;
            }

            // user is username, but we're about to rewrite it.  Grab it quick!
            $username = $user;
            $udata    = $this->CI->um->getUserByUsername($user);

            if (!empty($udata)) { 
                $user = $udata[0]->ID;
            } else { 
                return false; 
            }

            $eid   = (int)$xml->action->event_id;
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
            
            $is_evt_admin  = $this->CI->uam->hasPerm($user, $eid, $rtype);
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
     * Does the work to add the track
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->model('event_track_model', 'etm');
        $this->CI->load->model('event_model', 'em');

        $eid  = (int)$this->xml->action->event_id;
        $name = (string)$this->xml->action->track_name;
        $desc = (string)$this->xml->action->track_desc;
        
        // Add the track to the event
        $this->CI->etm->addEventTrack($eid, $name, $desc);
        $this->CI->em->cacheTrackCount($eid);
        return array(
            'output'=>'json',
            'data'=>array(
                'items'=>array(
                    'msg'=>'Success')
                )
            );
    }
}
