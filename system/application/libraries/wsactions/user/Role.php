<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Role {
	
	var $CI		= null;
	var $xml	= null;
	
	function Role($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	//-----------------------
	function run(){
		$this->CI->load->model('user_admin_model','uam');
		$type=$this->xml->action->type;
		
		if($type=='remove'){
			$aid=$this->xml->action->aid;
			$this->CI->uam->removePerm($aid);
		}elseif($type=='addevent'){
			$uid=$this->xml->action->uid;
			$rid=$this->xml->action->rid;
			$this->CI->uam->addPerm($uid,$rid,'event');
		}elseif($type=='addtalk'){
			$uid=$this->xml->action->uid;
			$rid=$this->xml->action->rid;
			$this->CI->uam->addPerm($uid,$rid,'talk');
		}
		
		return array('output'=>'json','items'=>array('msg'=>'Success'));
	}
	
}