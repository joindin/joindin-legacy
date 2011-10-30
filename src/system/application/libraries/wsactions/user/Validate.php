<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Validate extends BaseWsRequest {
    
    var $CI		= null;
    var $xml	= null;
    
    public function Validate($xml) {
        $this->CI=&get_instance(); //print_r($this->CI);
        $this->xml=$xml;
    }
    /**
    * Only site admins can use this functionality
    */
    public function checkSecurity($xml) {
        //public function!
        return true;
    }
    
    public function run() {
        $this->CI->load->model('user_model');
        
        // check for a valid login
        $ret = array('msg'=>'Invalid user');;
        if (isset($this->xml->action->uid) && isset($this->xml->action->pass)) {
            // check to see if they're a valid user
            if ($this->CI->user_model->validate($this->xml->action->uid, $this->xml->action->pass, true)) {
                $ret = array('msg'=>'success');;
            }
        }
        return array('output'=>'json','data'=>array('items'=>$ret));
    }
    
}

?>
