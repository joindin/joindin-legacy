<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* "Claiming an event" - requesting to be added as an admin */
class Claim extends BaseWsRequest {

    var $CI		= null;
    var $xml	= null;

    public function Claim($xml) {
        $this->CI=&get_instance(); //print_r($this->CI);
        $this->xml=$xml;
    }
    public function checkSecurity($xml) {
        // public function!
        return true;
    }
    //-----------------------
    public function run() {
        $this->CI->load->library('wsvalidate');
        $this->CI->load->model('user_admin_model');
        $this->CI->load->model('event_model');

        $rules=array(
            'eid'=>'required|isevent'
        );
        $eid=$this->xml->action->eid;
        $ret=$this->CI->wsvalidate->validate($rules, $this->xml->action);
        if (!$ret) {
            // Passed validation...
            // Be sure they're logged in
            if ($this->CI->wsvalidate->validate_loggedin()) { error_log('logged in!');
                $uid=$this->CI->session->userdata('ID');
                $arr=array(
                    'uid' 	=> $uid,
                    'rid' 	=> $eid,
                    'rtype'	=> 'event',
                    'rcode'	=> 'pending'
                );
                // Be sure we don't already have a claim pending
                $q=$this->CI->db->get_where('user_admin', $arr);
                $ret=$q->result();
                if (isset($ret[0]->ID)) {
                    return array('output'=>'json','data'=>array('items'=>array('msg'=>'Fail: Duplicate Claim!')));
                } else {
                    //we're good isert the row!
                    $this->CI->db->insert('user_admin', $arr);
                    return array('output'=>'json','data'=>array('items'=>array('msg'=>'Success')));
                }
            }
        }
        return array('output'=>'json','items'=>array('msg'=>'Fail'));
    }

}
