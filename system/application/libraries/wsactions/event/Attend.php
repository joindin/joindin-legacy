<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Attend {
	
	var $CI		= null;
	var $xml	= null;
	
	function Attend($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	//-----------------------
	function run(){
		$this->CI->load->library('wsvalidate');
		$this->CI->load->model('user_attend_model');
		
		$rules=array(
			'eid'		=>'required|isevent',
			//'reqkey'	=>'required|reqkey'
		);
		$eid=$this->xml->action->eid;
		$ret=$this->CI->wsvalidate->validate($rules,$this->xml->action);
		if(!$ret){
			//see if were logged in - if not, we return the redirect: message back
			if($this->CI->wsvalidate->validate_loggedin()){				
				$uid=$this->CI->session->userdata('ID');
				
				//check to see if they have a record - if they do, remove
				//if they don't, add...
				$this->CI->user_attend_model->chgAttendStat($uid,$eid);
				
				return array('msg'=>'Success');
				
			}else{ return array('msg'=>'redirect:/user/login'); }
		}else{ return array('msg'=>'Fail'); }
	}
	//-----------------------
}
?>