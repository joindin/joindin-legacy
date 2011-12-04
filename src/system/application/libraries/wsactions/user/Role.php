<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Role extends BaseWsRequest {
    
    var $CI		= null;
    var $xml	= null;
    
    public function Role($xml) {
        $this->CI=&get_instance(); //print_r($this->CI);
        $this->xml=$xml;
    }
    /**
    * Only site admins can use this functionality
    */
    public function checkSecurity($xml) {
        $this->CI->load->model('user_model');
        
        // Check for a valid login
        //if ($this->isValidLogin($xml) || $this->CI->user_model->isAuth()) {
        if ($this->CI->user_model->isAuth()) {
            // Now check to see if they're a site admin
            $user=$this->session->userdata('username');
            if (!$this->CI->user_model->isSiteAdmin($user)) {
                return false;
            } else { return true; }
            
        } else { return false; }
    }
    //-----------------------
    public function run() {
        $this->CI->load->model('user_admin_model','uam');
        $type=$this->xml->action->type;
        
        if ($type=='remove') {
            $aid=$this->xml->action->aid;
            $this->CI->uam->removePerm($aid);
        } elseif ($type=='addevent') {
            $uid=$this->xml->action->uid;
            $rid=$this->xml->action->rid;
            $this->CI->uam->addPerm($uid, $rid,'event');
        } elseif ($type=='addtalk') {
            $uid=$this->xml->action->uid;
            $rid=$this->xml->action->rid;
            $this->CI->uam->addPerm($uid, $rid,'talk');
        }
        
        return array('output'=>'json','items'=>array('msg'=>'Success'));
    }
    
}
