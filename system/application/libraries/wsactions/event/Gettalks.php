<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Gettalks extends BaseWsRequest {
	
	var $CI	= null;
	var $xml= null;
	
	public function Gettalks($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	public function checkSecurity($xml){
		//public function!
		// Be sure they've given us an event ID
		if(!isset($xml->action->eid)){ return false; }
		
		return ($this->isValidLogin($xml)) ? true : false;
	}
	//-----------------------
	public function run(){
		$this->CI->load->model('event_model');
		$eid=$this->xml->action->eid;
					
		$ret=$this->CI->event_model->getEventTalks($eid);
		return array('output'=>'json','data'=>array('items'=>$ret));
	}
	
}
?>