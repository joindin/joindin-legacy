<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Isspam extends BaseWsRequest {
    
    var $CI	= null;
    var $xml= null;
    
    public function Isspam($xml) {
        $this->CI=&get_instance(); //print_r($this->CI);
        $this->xml=$xml;
    }
    public function checkSecurity($xml) {
        // public method!
        return true;
    }
    //-----------------------
    public function run() {
        $this->CI->load->model('user_model');
        
        $cid	= $this->xml->action->cid;
        $rtype	= $this->xml->action->rtype;
        $tid    = $this->xml->action->tid;
        
        $msg='Spam comment on : ' . $this->CI->config->site_url() . $rtype . '/view/' . $tid . "#comment-" . $cid;

        $admin_emails=$this->CI->user_model->getSiteAdminEmail();
        foreach ($admin_emails as $user) {
            mail($user->email,'Suggested spam comment!', $msg,'From: ' . $this->CI->config->item('email_info'));
        }
        
        return array('output'=>'json','data'=>array('items'=>array('msg'=>'Success')));
    }
}
