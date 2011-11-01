<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Status {
    
    var $CI		= null;
    var $xml	= null;
    
    public function Status($xml) {
        $this->CI=&get_instance(); //print_r($this->CI);
        $this->xml=$xml;
    }
    public function checkSecurity($xml) {
        if ($this->isValidLogin($xml)) {
            if ($this->CI->user_model->isSiteAdmin($xml->auth->user)) {
                return true;
            } else { return false; }
        } else { return false; }
    }
    //-----------------------
    public function run() {
        $this->CI->load->model('user_model','um');
        $uid=$this->xml->action->uid;
        
        $this->CI->um->toggleUserStatus($uid);
        return array('output'=>'json','items'=>array('msg'=>'Success'));
    }
    
}
