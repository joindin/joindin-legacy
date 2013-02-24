<?php 
/**
 * Joindin webservice for updating tracks
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
 * Joindin webservice for updating tracks
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Updatetrack extends BaseWsRequest
{
    protected $CI  = null;
    protected $xml = null;
    
    /**
     * Instantiates webservice for updating tracks
     *
     * @param string $xml XML sent from service
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Ensures that the user of the API is allowed to call it. In this case
     * you must be the event admin or site admin
     *
     * @param string $xml XML sent to webservice
     *
     * @return boolean
     */
    public function checkSecurity($xml) 
    {
        $this->CI->load->model('user_model', 'um');
        $this->CI->load->model('user_admin_model', 'uam');
        
        // Check for a valid logged in user, either via the auth or sessions
        if ($this->CI->um->isAuth() || $this->isValidLogin($xml)) {
            // They either need to be an admin of the event or a site admin
            
            $user = false;

            if ($this->CI->um->isAuth()) {
                $user = $this->CI->session->userdata('username');
            } else {
                $user = (string)$xml->auth->user;
            }

            // user is username, but we're about to rewrite it.  Grab it quick!
            $username = $user;

            if (!is_int($user)) { 
                $udata = $this->CI->um->getUserByUsername($user);
                if (!empty($udata)) { 
                    $user = $udata[0]->ID;
                } else { 
                    return false;
                }
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
     * Does the work to update a track
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->model('event_track_model', 'etm');
        $arr = array(
            'track_name'  => (string)$this->xml->action->track_name,
            'track_desc'  => (string)$this->xml->action->track_desc,
            'track_color' => (string)$this->xml->action->track_color,
        );

        $tid = (string)$this->xml->action->track_id;
        
        // Add the track to the event
        $this->CI->etm->updateEventTrack($tid, $arr);
        return array(
            'output'=>'json',
            'data'=>array(
                'items'=>array(
                    'msg'=>'Success')
                )
            );
    }
}

