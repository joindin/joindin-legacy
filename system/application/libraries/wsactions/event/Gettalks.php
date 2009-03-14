<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Gettalks {
	
	var $CI	= null;
	var $xml= null;
	
	function Gettalks($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	//-----------------------
	function run(){
		$this->CI->load->model('event_model');
		$eid=$this->xml->action->eid;
					
		$ret=$this->CI->event_model->getEventTalks($eid);
		return array('output'=>'json','items'=>$ret);
	}
	
}
?>