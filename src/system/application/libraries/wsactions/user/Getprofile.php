<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Getprofile extends BaseWsRequest {
	
	var $CI		= null;
	var $xml	= null;
	
	public function Getprofile($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	/**
	* Only site admins can use this functionality
	*/
	public function checkSecurity($xml){
		//public function!
		return true;
	}
	
	public function run(){
		$this->CI->load->model('speaker_profile_model','spm');
		$token=$this->xml->action->spid;
		
		// Get the content information from the speaker record for this key
		$ret=$this->spm->getDetailByToken($token);
		$ret=array('speaker_data'=>$ret);
		return array('output'=>'json','data'=>array('items'=>$ret));
	}
	
}

?>
