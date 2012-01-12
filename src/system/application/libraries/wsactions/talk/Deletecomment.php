<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Deletecomment extends BaseWsRequest {
    
    var $CI	= null;
    var $xml= null;
    
    public function Deletecomment($xml) {
        $this->CI=&get_instance(); //print_r($this->CI);
        $this->xml=$xml;
    }
    /**
    * One event admins and talk owners can remove comments
    */
    public function checkSecurity($xml) {
        $this->CI->load->model('user_admin_model','uam');
        $this->CI->load->model('user_model');
        
        // Check for a valid login
        if ($this->isValidLogin($xml)) {
            $udata=$this->CI->user_model->getUser($xml->auth->user);
            
            // Now check to see if they're a site admin
            $is_site=$this->CI->user_model->isSiteAdmin($xml->auth->user);
            $is_talk=$this->CI->uam->hasPerm($udata[0]->ID, $xml->action->tid,'talk');

            
            return ($is_site || $is_talk) ? true : false;
                        
        }

        // check for valid site js codes
        if ($this->checkPublicKey()) {
            return true;
        }
        return false; 
    }
    //-----------------------
    public function run() {
        // Be sure we're getting out right input
        if (!isset($this->xml->action->cid)) {
            return array('output'=>'json','data'=>array('items'=>array('msg'=>'Missing Input Values!')));
        }
        
        $this->CI->load->library('wsvalidate');
        $this->CI->load->model('talk_comments_model','tcm');
        
        $com_id=$this->xml->action->cid;
        $this->CI->tcm->deleteComment($com_id);
        
        return array('output'=>'json','data'=>array('items'=>array('msg'=>'Success')));
    }
    
}
