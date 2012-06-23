<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Remove a user's claim on a talk

class RemoveTalkClaim extends BaseWsRequest {

    var $CI		= null;
    var $xml	= null;

    public function RemoveTalkClaim($xml) {
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
            $user=$this->CI->session->userdata('username');
            if (!$this->CI->user_model->isSiteAdmin($user)) {
                return false;
            } else { return true; }

        } else { return false; }
    }

    //-----------------------
    public function run() {

        $this->CI->load->model('pending_talk_claims_model');

        $result = array();
        $claim_id = (int)$this->xml->action->claim_id;
        $css_row_id = (string)$this->xml->action->css_row_id;
        $result['claim_id'] = $claim_id;
        $result['css_row_id'] = $css_row_id;

        $this->CI->pending_talk_claims_model->deleteClaim($claim_id);

        $result['msg'] = 'Success';
        return array('output'=>'json','items'=>$result);
    }

}
