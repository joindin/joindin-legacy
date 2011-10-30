<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Getdetail extends BaseWsRequest {
    
    var $CI		= null;
    var $xml	= null;
    
    public function Getdetail($xml) {
        $this->CI=&get_instance(); //print_r($this->CI);
        $this->xml=$xml;
    }

    public function checkSecurity($xml) {
        //public function!
        return true;
    }
    
    public function run() {
        $this->CI->load->model('user_model');
        $this->CI->load->library('wsvalidate');

        // uid can be either numeric user id or username
        $uid=$this->xml->action->uid;

        $rules=array(
            'uid'	=>'required'
        );
        $ret=$this->CI->wsvalidate->validate($rules, $this->xml->action);

        if (!$ret) {
            $ret=$this->CI->user_model->getUserDetail(sprintf('%s', $uid));

            return array('output'=>'json','data'=>array('items'=>$ret));
        } else {
            return array('output'=>'json','data'=>array('items'=>array('msg'=>'Required field uid missing!')));
        }
    }
    
}

?>
