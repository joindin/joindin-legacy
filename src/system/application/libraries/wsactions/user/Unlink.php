<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Unlink a user from a talk

class Unlink extends BaseWsRequest {

    var $CI		= null;
    var $xml	= null;

    public function Unlink($xml) {
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

        $this->CI->load->model('talk_speaker_model');

        $result = array();
        $talk_id = (int)$this->xml->action->talk_id;
        $speaker_id = (int)$this->xml->action->speaker_id;
        $css_row_id = (string)$this->xml->action->css_row_id;
        $result['talk_id'] = $talk_id;
        $result['speaker_id'] = $speaker_id;
        $result['css_row_id'] = $css_row_id;

        $this->CI->talk_speaker_model->unlinkSpeaker($talk_id, $speaker_id);

        $result['msg'] = 'Success';
        return array('output'=>'json','items'=>$result);
    }

}
