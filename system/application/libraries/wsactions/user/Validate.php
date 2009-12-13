<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Validate extends BaseWsRequest {
	
	var $CI		= null;
	var $xml	= null;
	
	public function Validate($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	/**
	* Only site admins can use this functionality
	*/
	public function checkSecurity($xml){
		//public function!
		return ($this->isValidLogin($xml)) ? true : false;
	}
	
	public function run(){
		//if we get here, we have a good login
		$ret=array('success');
		return array('output'=>'json','data'=>array('items'=>$ret));
	}
	
}

?>