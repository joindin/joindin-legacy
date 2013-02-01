<?php 
/**
 * Joindin webservice for getting event details
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
 * Joindin webservice for getting event details
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
     * Instantiates the webservice for getting event details
     *
     * @param string $xml XML sent to webservice
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Public API
     *
     * @param string $xml XML sent to webservice
     *
     * @return true
     */
    public function checkSecurity($xml) 
    {
        //public function!
        return true;
    }

    /**
     * Does the work to get the event details
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->library('wsvalidate');
        
        $rules = array(
            'event_id' =>'required|isevent'
        );

        $eid   = $this->xml->action->event_id;
        $valid = $this->CI->wsvalidate->validate($rules, $this->xml->action);
        if (!$valid) {
            $this->CI->load->model('event_model');
            $this->CI->load->model('event_track_model');
            $this->CI->load->model('user_attend_model');
            $ret = $this->CI->event_model->getEventDetail((string)$eid);

            // identify user so we can do the attending (or not if they're not 
            // identified)
            $uid  = false;
            $user = $this->CI->user_model
                ->getUserByUsername((string)$this->xml->auth->user);
            if ($user) {
                $uid = $user[0]->ID;
            }

            if ($uid) {
                // Check to see if it's provate and if they're allowed
                if ($ret[0]->private == "Y") {
                    $this->CI->load->model('invite_list_model', 'ilm');
                    $is_invited = $this->ilm->isInvited($ret[0]->ID, $uid);
                    if (!$is_invited) {
                        //If not, return an error message...
                        return array(
                            'output'=>'json',
                            'data'=>array(
                                'items'=>array(
                                    'msg'=>'Not authorized for private event!'
                                )
                            )
                        );
                    }
                }
                $ret[0]->user_attending = $this->CI->user_attend_model
                    ->chkAttend($uid, $ret[0]->ID);
            }

            // add a list of tracks
            $ret[0]->tracks = $this->CI->event_track_model->getEventTracks($eid);
            return array(
                'output'=>'json',
                'data'=>array(
                    'items'=>$ret)
                );
        } else {
            return array(
                'output'=>'json',
                'data'=>array(
                    'items'=>array(
                        'msg'=>'Invalid Event ID!')
                    )
                );
        }
    }
}
