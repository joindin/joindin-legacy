<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Web Service Action: Add an event comment
 */
class Addcomment extends BaseWsRequest {
    
    var $CI	= null;
    var $xml= null;
    
    public function Addcomment($xml) {
        $this->CI=&get_instance(); //print_r($this->CI);
        $this->xml=$xml;
    }
    public function checkSecurity($xml) {
        // users can comment anonymously, don't require login
        return true;
    }
    //-----------------------
    public function run() {
        $this->CI->load->library('wsvalidate');
        $unq = false;
        
        $rules=array(
            'event_id'	=>'required',
            'comment'	=>'required'
        );
        $ret=$this->CI->wsvalidate->validate($rules, $this->xml->action);
        if ($ret) {
            return $this->throwError($ret);
        }
        $unq=$this->CI->wsvalidate->validate_unique('event_comments', $this->xml->action);
        if ($unq) {
            $in=(array)$this->xml->action;			
            $user=$this->CI->user_model->getUser($this->xml->auth->user);
            if ($user && !$this->isValidLogin($this->xml)) {
                return $this->throwError('Invalid permissions');
            }
            
            // Check to see if you can submit a comment to the event....
            $this->CI->load->model('event_model');
            $event_detail=$this->CI->event_model->getEventDetail($in['event_id']);
            
            $adv_mo=strtotime('+3 months', $event_detail[0]->event_start);
            if (time()>$adv_mo) {
                return $this->throwError('Comments not allowed for this talk!');
            }

            $arr=array(
                'event_id'	=> $in['event_id'],
                'comment'	=> $in['comment'],
                'source'	=> isset($in['source']) ? $in['source'] : 'api',
                'date_made'	=> time(),
                'active'	=> 1
            );
            if ($user) {
                $arr['user_id'] = $user[0]->ID;
                $arr['cname'] = $user[0]->full_name;
            }
            $this->CI->db->insert('event_comments', $arr);
            return $this->throwError('Comments added');
        } else { 
            if (!$unq) { $ret='Non-unique entry!'; }
            return $this->throwError($ret);
        }
        return $ret;
    }
}
?>
