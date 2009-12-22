<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Web Service Action: Add a talk comment
 */
class Addcomment extends BaseWsRequest {
	
	var $CI	= null;
	var $xml= null;
	
	public function Addcomment($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	public function checkSecurity($xml){
		$this->CI->load->model('user_model');
		
		// Check to see if what they gave us is a valid login
		// Check for a valid login
		return ($this->isValidLogin($xml)) ? true : false;
	}
	//-----------------------
	public function run(){
		$this->CI->load->library('wsvalidate');
		
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
			}elseif($this->CI->user_model->isAuth()){
				$arr['user_id']=$this->session->userdata('ID');
			}
			//print_r($arr);
			
			$this->CI->db->insert('talk_comments',$arr);
			$ret=array('output'=>'msg','data'=>array('msg'=>'Comment added!'));
		}else{ 
			if(!$unq){ $ret='Non-unique entry!'; }
			$ret=array('output'=>'msg','data'=>array('msg'=>$ret));
		}
		return $ret;
	}
}