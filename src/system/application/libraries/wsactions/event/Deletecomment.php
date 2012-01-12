<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Deletecomment extends BaseWsRequest {
    
    var $CI	= null;
    var $xml= null;
    
    public function Deletecomment($xml) {
        $this->CI=&get_instance(); //print_r($this->CI);
        $this->xml=$xml;
    }
    /*
    * Only site and event admins can remove comments from events
    */
    public function checkSecurity($xml) {
        // Check for a valid login
        if ($this->isValidLogin($xml) || $this->checkPublicKey()) {
            // Check to be sure they've given us an event ID & comment ID
            if (!isset($xml->action->eid) || !isset($xml->action->cid)) {
                return false;
            }
            
            $eid=$xml->action->eid;
            // Now check to see if they're a site admin or an event admin
            $is_site	= $this->CI->user_model->isSiteAdmin($xml->auth->user);
            $is_evt		= $this->CI->user_model->isAdminEvent($eid, $xml->auth->user);
            $is_js		= $this->checkPublicKey();
            return ($is_site || $is_evt || $is_js) ? true : false;
            
        } else { return false; }
    }
    //-----------------------
    public function run() {
        $this->CI->load->library('wsvalidate');
        $this->CI->load->model('event_comments_model','ecm');
        
        $com_id=$this->xml->action->cid;
        $this->CI->ecm->deleteComment($com_id);

        return array('output'=>'json','data'=>array('items'=>array('msg'=>'Success')));
    }
    
}
