<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Getdetail {
	
	var $CI	= null;
	var $xml= null;
	
	function Getdetail($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	//-----------------------
	function run(){
		$id=$this->xml->action->talk_id;
		
		$this->CI->load->model('talks_model');
		$ret=$this->CI->talks_model->getTalks($id);
		//return array('msg'=>'valid'); 
		return $ret;
	}
}