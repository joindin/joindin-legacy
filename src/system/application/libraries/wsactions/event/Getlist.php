<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Getlist extends BaseWsRequest {
	
	private $CI	= null;
	private $xml= null;
	private $_valid_types = array(
		'hot','upcoming','past','pending'
	);
	
	public function Getlist($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	public function checkSecurity($xml){
		// public method!
		return true;
	}
	//-----------------------
	public function run(){
		$this->CI->load->library('wsvalidate');
		
		$rules=array(
			'event_type'		=>'required',
		);
		$valid=$this->CI->wsvalidate->validate($rules,$this->xml->action);
		if(!$valid){
			$this->CI->load->model('event_model');
			$this->CI->load->model('user_attend_model');
			
			$type=strtolower($this->xml->action->event_type);
			if(!in_array($type,$this->_valid_types)){
				return array('output'=>'json','data'=>array('items'=>array('msg'=>'Invalid event type!')));
			}
			// if it's pending, they need to be an admin to get it
			if($type=='pending' && !$this->CI->user_model->isSiteAdmin($this->xml->auth->user)){
				return array('output'=>'json','data'=>array('items'=>array('msg'=>'Access denied')));
			}else{ $pending=true; }
			
			switch ($type) {
			    case 'hot':
			        $events = $this->CI->event_model->getHotEvents(null);
			        break;
			    case 'upcoming':
			        $events = $this->CI->event_model->getUpcomingEvents(null);
			        break;
			    case 'past':
			        $events = $this->CI->event_model->getPastEvents(null);
			        break;
				case 'pending':
					$events = $this->CI->event_model->getEventDetail(null,null,null,$pending);
			    /*default:
			        $events = $this->event_model->getEventDetail(null,null,null,$pending);
			        break;*/
			}

			// identify user so we can do the attending (or not if they're not identified)
			$uid = false;
			$user=$this->CI->user_model->getUser($this->xml->auth->user);
			if($user) {
				$uid = $user[0]->ID;
			}

			// Filter out a few things first
			foreach($events as $k=>$evt){
				unset($events[$k]->event_lat,$events[$k]->event_long,$events[$k]->score);
				
				// Remove the private events for now...
				if($evt->private==1){ unset($events[$k]); }
				
				if($uid) {
					$evt->user_attending = $this->CI->user_attend_model->chkAttend($uid, $evt->ID);
				}
			}
			return array('output'=>'json','data'=>array('items'=>$events));
		}else{
			return array('output'=>'json','data'=>array('items'=>array('msg'=>'Invalid event type!')));
		}
	}
	
}
?>
