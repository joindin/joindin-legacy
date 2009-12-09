<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
* TODO: make this work with any given type - blog, event, talk
*/ 
class Getdetail extends BaseWsRequest {
	
	var $CI	= null;
	var $xml= null;
	
	public function Getdetail($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	public function checkSecurity($xml){
		// public method!
		return ($this->isValidLogin($xml)) ? true : false;
	}
	//-----------------------
	public function run(){
		$id=$this->xml->action->cid;
		
		$this->CI->load->model('event_model');
		//$ret=$this->CI->event_model->getEventDetail($id);
		//return array('msg'=>'valid'); 
		$items=array('title'=>'title #1');
		$ret=array('output'=>'xml','data'=>array('items'=>$items));
		return $ret;
	}
}