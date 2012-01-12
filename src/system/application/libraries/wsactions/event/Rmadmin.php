<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Rmadmin extends BaseWsRequest {
    
    var $CI	= null;
    var $xml= null;
    
    public function Rmadmin($xml) {
        $this->CI=&get_instance(); //print_r($this->CI);
        $this->xml=$xml;
    }
    /**
    * Right now, only site admins can add events via the web interface
    */
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
            $eid	= (int)$xml->action->eid;
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
        if (!isset($this->xml->action->eid) || !isset($this->xml->action->username)) {
            return array('output'=>'json','data'=>array('items'=>array('msg'=>'Required fields missing!')));
        }
        $this->CI->load->model('user_admin_model','uam');
        $this->CI->load->model('user_model','um');
        
        $user	= $this->xml->action->username;
        $eid	= (int)$this->xml->action->eid;
        $type	= 'event';
        
        // Event ID must be an integer
        if (!is_int($eid)) { return array('output'=>'json','data'=>array('items'=>array('msg'=>'Invalid Event ID!'))); }
        
        if (!is_int($user)) { 
            $udata=$this->CI->um->getUser($user);
            if (!empty($udata)) { 
                $user=$udata[0]->ID;
            } else { return false; }
        }
        
        // Check to see if they're already an admin 
        $this->CI->uam->removeRidPerm($user, $eid, $type);
        return array('output'=>'json','data'=>array('items'=>array('msg'=>'Success')));
    }	
}

?>
