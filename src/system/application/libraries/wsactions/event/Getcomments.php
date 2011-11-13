<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Getcomments extends BaseWsRequest {
    
    var $CI	= null;
    var $xml= null;
    
    public function Getcomments($xml) {
        $this->CI=&get_instance(); 
        $this->xml=$xml;
    }
    public function checkSecurity($xml) {
        //public function!
        // Be sure they've given us an event ID
        if (!isset($xml->action->event_id)) { return false; }

        return true;
        
    }
    //-----------------------
    public function run() {
        $this->CI->load->library('wsvalidate');
        
        $rules=array(
            'event_id'		=>'required|isevent',
        );
        $eid=$this->xml->action->event_id;
        $valid=$this->CI->wsvalidate->validate($rules, $this->xml->action);
        if (!$valid) {
            $this->CI->load->model('event_comments_model');
            $ret=$this->CI->event_comments_model->getEventComments($eid);
            return array('output'=>'json','data'=>array('items'=>$ret));
        } else {
            return array('output'=>'json','data'=>array('items'=>array('msg'=>'Invalid Event ID!')));
        }
    }
    
}
?>
