<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Getlist extends BaseWsRequest {
	
	var $CI	= null;
	var $xml= null;
	
	public function Getlist($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	public function checkSecurity($xml){
		// public method!
		return ($this->isValidLogin($xml)) ? true : false;
	}
	//-----------------------
	public function run(){
		$this->CI->load->model('event_model');
		$eid=$this->xml->action->eid;
		$ret=$this->CI->event_model->getEventDetail($eid);

		//Sort them by name...
		$names	= array();
		$tmp	= array();
		foreach($ret as $k=>$v){ $names[$v->event_name]=$k; }
		ksort($names);
		foreach($names as $k=>$v){ $tmp[]=$ret[$v]; }

		return array('output'=>'json','data'=>array('items'=>$tmp));
	}
	
}
?>