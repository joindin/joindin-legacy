<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Getcomments extends BaseWsRequest {
	
	var $CI	= null;
	var $xml= null;
	
	public function Getcomments($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	public function checkSecurity($xml){
		// public method!
		return ($this->isValidLogin($xml)) ? true : false;
	}
	//-----------------------
	public function run(){
		$id=$this->xml->action->talk_id;
		
		$this->CI->load->model('talks_model');
		$ret=$this->CI->talks_model->getTalkComments($id);
		return array('items'=>$ret);
	}
}