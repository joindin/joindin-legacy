<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Claim {
	
	var $CI		= null;
	var $xml	= null;
	
	function Claim($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	//-----------------------
	function run(){
		$this->CI->load->library('wsvalidate');
		$this->CI->load->model('user_admin_model');
		$this->CI->load->model('talks_model');
		
		$rules=array(
			'tid'		=>'required|istalk',
			//'reqkey'	=>'required|reqkey'
		);
		$tid=$this->xml->action->tid;
		$ret=$this->CI->wsvalidate->validate($rules,$this->xml->action);
		if(!$ret){		
			if($this->CI->wsvalidate->validate_loggedin()){				
				$uid=$this->CI->session->userdata('ID');
				$ret=$this->CI->talks_model->getTalks($tid);
				$talk_det=$ret[0];
				
				//insert a row into user_admin for the user/talk ID but with a code of "pending"
				$arr=array(
					'uid' 	=> $uid,
					'rid' 	=> $tid,
					'rtype'	=> 'talk',
					'rcode'	=> 'pending'
				);
				$this->CI->db->insert('user_admin',$arr);
				
				//send an email about the claim
				$to		= 'enygma@phpdeveloper.org';
				$subj	= 'Talk claim submitted! Go check!';
				$msg	= sprintf("
Talk claim has been submitted for talk \"%s\"

http://joind.in/talk/claim
				",$talk_det->talk_title);
				mail('enygma@phpdeveloper.org','Joind.in: Talk claim submitted! Go check!',$msg,'From: feedback@joind.in');
				
				//return the success message
				return array('output'=>'json','items'=>array('msg'=>'Success'));
			
			}else{ return array('output'=>'json','items'=>array('msg'=>'redirect:/user/login')); }
		}else{ return array('output'=>'json','items'=>array('msg'=>'Fail')); }
	}
	
}