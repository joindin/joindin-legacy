<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Getcomments extends BaseWsRequest {
    
    var $CI	= null;
    var $xml= null;
    
    public function Getcomments($xml) {
        $this->CI=&get_instance(); //print_r($this->CI);
        $this->xml=$xml;
    }
    /**
    * Just be sure they've given us a valid login
    */
    public function checkSecurity($xml) {
        // public function
        return true;
    }
    
    public function run() {
        // Get the comments the user has put on events and talks
        $this->CI->load->library('wsvalidate');
        $this->CI->load->model('user_model');
        $this->CI->load->model('talk_comments_model');
        $this->CI->load->model('event_comments_model');
        
        $rules=array(
            'username'	=>'required'
        );
        $ret=$this->CI->wsvalidate->validate($rules, $this->xml->action);
        if (!$ret) {
            // We're good - get the user's information
            $comments=array();
            $restrict=(isset($this->xml->action->type)) ? strtolower($this->xml->action->type) : false;

            $udata=$this->CI->user_model->getUser($this->xml->action->username);
            if (empty($udata)) {
                return array('output'=>'json','data'=>array('items'=>array('msg'=>'Invalid username!')));
            }
            
            if (!$restrict || $restrict=='talk') {
                // First, the talk comments...
                $uc_talk=$this->CI->talk_comments_model->getUserComments($udata[0]->ID);
                foreach ($uc_talk as $k=>$v) {
                    // We're just going to remove private comments for now
                    if ($v->private==1) { continue; }
                    $v->type='talk'; $comments[]=$v;
                }
            }
            if (!$restrict || $restrict=='event') {
                // Now the event comments
                $uc_event= $this->CI->event_comments_model->getUserComments($udata[0]->ID);
                foreach ($uc_event as $k=>$v) {
                    $v->type='event'; $comments[]=$v;
                }
            }
            
            return array('output'=>'json','data'=>array('items'=>$comments));
        } else {
            return array('output'=>'json','data'=>array('items'=>array('msg'=>'Required fields missing!')));
        }
    }
}

?>
