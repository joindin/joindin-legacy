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
		$this->CI->load->model('user_model');
		$this->CI->load->model('talks_model');
		$this->CI->load->model('event_model');
		
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

				$arr=array(
					'uid' 	=> $uid,
					'rid' 	=> $tid,
					'rtype'	=> 'talk',
					'rcode'	=> 'pending'
				);
				// Be sure we don't already have a claim pending
				$q=$this->CI->db->get_where('user_admin',$arr);
				$ret=$q->result();
				if(isset($ret[0]->ID)){
				    return array('output'=>'json','items'=>array('msg'=>'Fail: Duplicate Claim!'));
				}else{
					$to=array();
					
					$admin_emails=$this->CI->user_model->getSiteAdminEmail();
					foreach($admin_emails as $user){ $to[]=$user->email; }
					
					// See if there's an admin for the event
					$evt_admin=$this->CI->event_model->getEventAdmins($talk_det->event_id);
					if(count($evt_admin)>0){
						foreach($evt_admin as $k=>$v){ $to[]=$v->email; }
					}
					
				    //insert a row into user_admin for the user/talk ID but with a code of "pending"
				    $this->CI->db->insert('user_admin',$arr);

				    //send an email about the claim
				    $subj	= 'Talk claim submitted! Go check!';
				    $msg	= sprintf("
Talk claim has been submitted for talk \"%s\"

Visit the link below to approve or deny the talk. Note: you must
be logged in to get to the \"Claims\" page for the event!

http://joind.in/event/claim/%s
				    ",$talk_det->talk_title,$talk_det->event_id);
				
					foreach($to as $email_addr){
				    	mail($email_addr,'Joind.in: Talk claim submitted! Go check!',$msg,'From: feedback@joind.in');
					}
				    //return the success message
				    return array('output'=>'json','items'=>array('msg'=>'Success'));
				}
			
			}else{ return array('output'=>'json','items'=>array('msg'=>'redirect:/user/login')); }
		}else{ return array('output'=>'json','items'=>array('msg'=>'Fail')); }
	}
	
}