<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Web Service Action: Add an event comment
 */
class Addcomment extends BaseWsRequest {
	
	var $CI	= null;
	var $xml= null;
	
	public function Addcomment($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	public function checkSecurity($xml){
		// users can comment anonymously, don't require login
		return true;
	}
	//-----------------------
	public function run(){
		$this->CI->load->library('wsvalidate');
		$unq = false;
		
		$rules=array(
			'event_id'	=>'required',
			'comment'	=>'required'
		);
		$ret=$this->CI->wsvalidate->validate($rules,$this->xml->action);
		if($ret) {
			return array('output'=>'json','data'=>array('items'=>array('msg'=>$ret)));
		}
		$unq=$this->CI->wsvalidate->validate_unique('event_comments',$this->xml->action);
		if($unq){
			$in=(array)$this->xml->action;			
			$user=$this->CI->user_model->getUser($this->xml->auth->user);
			if($user && !$this->isValidLogin($this->xml)) {
				return array('output'=>'json','data'=>array('items'=>array('msg'=>'Invalid permissions')));
			}

			$arr=array(
				'event_id'	=> $in['event_id'],
				'comment'	=> $in['comment'],
				'date_made'	=> time(),
				'active'	=> 1
			);
			if($user) {
				$arr['user_id'] = $user[0]->ID;
				$arr['cname'] = $user[0]->full_name;
			}
			$this->CI->db->insert('event_comments',$arr);
			$ret=array('output'=>'json','data'=>array('items'=>array('msg'=>'Comment added!')));
		}else{ 
			if(!$unq){ $ret='Non-unique entry!'; }
			$ret=array('output'=>'json','data'=>array('items'=>array('msg'=>$ret)));
		}
		return $ret;
	}
}
?>
