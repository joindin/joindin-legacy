<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Getdetail extends BaseWsRequest {
	
	var $CI		= null;
	var $xml	= null;
	
	public function Getdetail($xml){
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
		$this->CI->load->model('user_model');
		$uid=$this->xml->action->uid;
		$ret=$this->CI->user_model->getUser($uid);
	
		
		//if they're not a site admin, remove some of the info
		if(!$this->CI->user_model->isSiteAdmin($this->xml->auth->user)){
			unset($ret[0]->email,$ret[0]->password,$ret[0]->admin,$ret[0]->active);
		}
		
		return array('output'=>'json','data'=>array('items'=>$ret));
	}
	
}

?>
