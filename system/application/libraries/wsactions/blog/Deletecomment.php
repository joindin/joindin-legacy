<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Deletecomment {
	
	var $CI	= null;
	var $xml= null;
	
	function Deletecomment($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	//-----------------------
	function run(){
		$this->CI->load->library('wsvalidate');
		$this->CI->load->model('blog_comments_model','bcm');
		
		$com_id=$this->xml->action->cid;
		$this->CI->bcm->deleteComment($com_id);
		
		return array('output'=>'json','items'=>array('msg'=>'Success'));
	}
	
}