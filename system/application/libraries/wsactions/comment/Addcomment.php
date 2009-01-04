<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Addcomment {
	
	var $CI	= null;
	var $xml= null;
	
	function Addcomment($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	//-----------------------
	function run(){
		$this->CI->load->library('wsvalidate');
		$id=$this->xml->action->id;
		
		$rules=array(
			'talk_id'	=>'required',
			'rating'	=>'required|range[1,5]',
			'comment'	=>'required',
			'private'	=>'required|range[0,1]'
		);
		$ret=$this->CI->wsvalidate->validate($rules,$this->xml->action);
		if(!$ret){
			$unq=$this->CI->wsvalidate->validate_unique('talk_comments',$this->xml->action);
		}

		if(!$ret && $unq){
			$in=(array)$this->xml->action;
			$arr=array(
				'talk_id'	=> $in['talk_id'],
				'rating'	=> $in['rating'],
				'comment'	=> $in['comment'],
				'date_made'	=> time(),
				'private'	=> $in['private'],
				'active'	=> 1
			);
			if(isset($this->xml->action->user_id)){
				$arr['user_id']=$in['user_id'];
			}
			//print_r($arr);
			
			$this->CI->db->insert('talk_comments',$arr);
			$ret=array('msg'=>'comment added');
		}else{ 
			if(!$unq){ $ret='Non-unique entry!'; }
			$ret=array('errors'=>$ret); 
		}
		return $ret;
	}
}