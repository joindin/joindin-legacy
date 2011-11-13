<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Addadmin extends BaseWsRequest {
    
    var $CI	= null;
    var $xml= null;
    
    public function Addadmin($xml) {
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
        $this->CI->load->library('sendemail');
        $this->CI->load->model('event_model','em');
        
        $user	= $this->xml->action->username;
        $eid	= (int)$this->xml->action->eid;
        $type	= 'event';
        
        // Event ID must be an integer
        if (!is_int($eid)) { return array('output'=>'json','data'=>array('items'=>array('msg'=>'Invalid Event ID!'))); }
        
        $udata=$this->CI->um->getUser($user);
        if (empty($udata)) { 
            //Let's search too...
            $udata=$this->CI->um->search($user);
            if (!empty($udata)) {
                if (count($udata)>1) {
                    return array('output'=>'json','data'=>
                        array('items'=>array('msg'=>'Too many results returned! Please try again...'))
                    );
                }
            } else {
                return array('output'=>'json','data'=>array('items'=>array('msg'=>'User not found!')));
            }
        }
        
        // Check to see if they're already an admin
        $perm = $this->CI->uam->getPendingPerm($udata[0]->ID, $eid, $type);
        if ($perm == null && !$this->CI->uam->hasPerm($udata[0]->ID, $eid, $type)) { 
            error_log('null');
            $this->CI->uam->addPerm($udata[0]->ID, $eid, $type);
            $evt=$this->CI->em->getEventDetail($eid);
            
            // Send them an email to let them know they've been added as an admin
            $this->CI->sendemail->sendAdminAdd($udata, $evt, $this->xml->auth->user);
            
            return array('output'=>'json','data'=>array('items'=>array('msg'=>'Success', 'user'=>$udata[0])));
        } elseif (isset($perm[0]) && $perm[0]->rcode == "pending") {
            $this->CI->uam->updatePerm($perm[0]->ID, array('rcode'=>''));
            return array('output'=>'json','data'=>array('items'=>array('msg'=>'Success', 'user'=>$udata[0])));
        } else {
            return array('output'=>'json','data'=>array('items'=>array('msg'=>'Duplicate request!')));
        }
    }
    
}

?>
