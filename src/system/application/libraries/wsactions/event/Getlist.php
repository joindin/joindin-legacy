<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Getlist extends BaseWsRequest {
    
    private $CI	= null;
    private $xml= null;
    private $_valid_types = array(
        'hot','upcoming','past','pending'
    );
    
    public function Getlist($xml) {
        $this->CI=&get_instance();
        $this->xml=$xml;
    }
    public function checkSecurity($xml) {
        // public method!
        return true;
    }
    //-----------------------
    public function run() {
        $this->CI->load->library('wsvalidate');
        
        $rules=array(
            'event_type'		=>'required',
        );
        $valid=$this->CI->wsvalidate->validate($rules, $this->xml->action);
        if (!$valid) {
            $this->CI->load->model('event_model');
            $this->CI->load->model('user_attend_model');
            
            $type=strtolower($this->xml->action->event_type);
            if (!in_array($type, $this->_valid_types)) {
                return array('output'=>'json','data'=>array('items'=>array('msg'=>'Invalid event type!')));
            }

            // retrieve the events
            $events = $this->CI->event_model->getEventsOfType($type);
            
            // identify user so we can do the attending (or not if they're not identified)
            $uid = false;
            $user=$this->CI->user_model->getUser($this->xml->auth->user);
            if ($user) {
                $uid = $user[0]->ID;
            }

            // Filter out a few things first
            foreach ($events as $k=>$evt) {
                unset($events[$k]->score);
                
                if ($uid) {
                    $evt->user_attending = $this->CI->user_attend_model->chkAttend($uid, $evt->ID);
                }
                if (($evt->private==1 || $evt->private == 'Y') && !$evt->user_attending) {
                    // not allowed to see the event!
                    unset($events[$k]);
                }
            }

            // Re-index the array, as the unsetting may have upset the apple-cart
            $events = array_values($events);

            return array('output'=>'json','data'=>array('items'=>$events));
        } else {
            return array('output'=>'json','data'=>array('items'=>array('msg'=>'Invalid event type!')));
        }
    }
    
}
?>
