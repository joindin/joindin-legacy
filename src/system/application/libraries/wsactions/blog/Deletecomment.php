<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Deletecomment extends BaseWsRequest {
    
    private $CI	= null;
    private $xml= null;
    
    public function Deletecomment($xml) {
        $this->CI=&get_instance(); //print_r($this->CI);
        $this->xml=$xml;
    }
    /**
    * Check to ensure that they:
    * - Passed in the valid login credentials
    * - They're for a valid login
    * - They're a site admin
    */
    public function checkSecurity($xml) {
        $this->CI->load->model('user_model');

        // Check for a valid login
        //if ($this->isValidLogin($xml)) {
        if ($this->CI->user_model->isAuth() && $this->checkPublicKey()) {
            // Be sure they gave us the blog entry ID and comment ID
            if (!isset($xml->action->bid) || !isset($xml->action->cid)) {
                return false;
            }
            $user=$this->CI->session->userdata('username');
            
            // Now check to see if they're a site admin
            if (!$this->CI->user_model->isSiteAdmin($user)) {
                return false;
            } else { return true; }
        } else { return false; }
    }
    //-----------------------
    public function run() {
        $this->CI->load->library('wsvalidate');
        $this->CI->load->model('blog_comments_model','bcm');
        
        $com_id=$this->xml->action->cid;
        $this->CI->bcm->deleteComment($com_id);
        
        return array('output'=>'json','data'=>array('items'=>array('msg'=>'Success')));
    }
    
}
