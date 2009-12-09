<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Getdetail extends BaseWsRequest {
	
	var $CI	= null;
	var $xml= null;
	
	public function Getdetail($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	public function checkSecurity($xml){
		//public function!
		return ($this->isValidLogin($xml)) ? true : false;
	}
	//-----------------------
	public function run(){
		$eid=$this->xml->action->eid;
		
		$this->CI->load->model('event_model');
		$ret=$this->CI->event_model->getEventDetail($eid);
		return array('output'=>'json','data'=>array('items'=>$ret));
	}
}
?>