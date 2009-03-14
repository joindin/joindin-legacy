<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Getlist {
	
	var $CI	= null;
	var $xml= null;
	
	function Getlist($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	//-----------------------
	function run(){
		$this->CI->load->model('event_model');
		$ret=$this->CI->event_model->getEventDetail();
		return array('output'=>'json','items'=>$ret);
	}
	
}
?>