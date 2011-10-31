<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Deletetrack extends BaseWsRequest {
    
    private $CI	= null;
    private $xml= null;
    
    public function Deletetrack($xml) {
        $this->CI=&get_instance(); //print_r($this->CI);
        $this->xml=$xml;
    }
    public function checkSecurity($xml) {
        $this->CI->load->model('user_model','um');
        $this->CI->load->model('user_admin_model','uam');
        
        // Check for a valid logged in user, either via the auth or sessions
        if ($this->CI->um->isAuth() || $this->isValidLogin($xml)) {
            // They either need to be an admin of the event or a site admin
            
            if ($this->CI->um->isAuth()) {
                $user=$this->CI->session->userdata('username');
            } elseif (!$this->CI->um->isAuth()) {
                $user=$xml->auth->user;
            }
            if (!is_int($user)) { 
                $udata=$this->CI->um->getUser($user);
                if (!empty($udata)) { 
                    $user=$udata[0]->ID;
                } else { return false; }
            }
            $eid	= (int)$xml->action->event_id;
            $rtype	= 'event';
            
            // Event ID must be an integer
            if (!is_int($eid)) { return array('output'=>'json','data'=>array('items'=>array('msg'=>'Invalid Event ID!'))); }
            
            $is_evt_admin = $this->CI->uam->hasPerm($user, $eid, $rtype);
            $is_site_admin= $this->CI->um->isSiteAdmin($user);
            
            if ($is_site_admin || $is_evt_admin) {
                return true;
            } else { return false; }
            
        } else { return false; }
    }
    //-----------------------
    public function run() {
        $this->CI->load->model('event_track_model','etm');
        $this->CI->load->model('talks_model','tm');
        
        $tid=(int)$this->xml->action->track_id;
        
        // Add the track to the event
        $ret=$this->CI->etm->deleteEventTrack($tid);
        if (!$ret) {
            return array('output'=>'json','data'=>array('items'=>array('msg'=>'Track still in use!')));
        } else {
            return array('output'=>'json','data'=>array('items'=>array('msg'=>'Success')));
        }
    }
}
?>
