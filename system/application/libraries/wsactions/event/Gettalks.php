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
		$this->CI->load->library('wsvalidate');
		
		$rules=array(
			'event_id'		=>'required|isevent',
			//'reqkey'	=>'required|reqkey'
		);
		$eid=$this->xml->action->event_id;
		$valid=$this->CI->wsvalidate->validate($rules,$this->xml->action);
		if(!$valid){
			$this->CI->load->model('event_model');
			$ret=$this->CI->event_model->getEventTalks($eid);
			return array('output'=>'json','data'=>array('items'=>$ret));
		}else{
			return array('output'=>'json','data'=>array('items'=>array('msg'=>'Invalid Event ID!')));
		}
	}
	
}
?>