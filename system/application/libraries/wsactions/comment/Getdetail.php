<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
* TODO: make this work with any given type - blog, event, talk
*/ 
class Getdetail extends BaseWsRequest {
	
	var $CI	= null;
	var $xml= null;
	
	public function Getdetail($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	public function checkSecurity($xml){
		// public method!
		return ($this->isValidLogin($xml)) ? true : false;
	}
	//-----------------------
	public function run(){
		$id		= $this->xml->action->cid;
		$type	= $this->xml->action->rtype;
		
		//$ret=$this->CI->event_model->getEventDetail($id);
		//return array('msg'=>'valid'); 
		
		//getTalkComments
		$ret=array();
		if($this->xml->action->rtype=='talk'){
			$this->CI->load->model('talks_model');
			$ret=$this->CI->talks_model->getTalkComments($id);
		}elseif($this->xml->action->rtype=='event'){
			$this->CI->load->model('event_model');
		}
		if(count($ret)>0){
			$items=array('title'=>'title #1');
			$ret=array('output'=>'json','data'=>array('items'=>$items));
		}else{
			$ret=array('output'=>'msg','data'=>array('msg'=>'Comment not found!'));
		}
		return $ret;
	}
}