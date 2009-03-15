<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Isspam {
	
	var $CI	= null;
	var $xml= null;
	
	function Isspam($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	//-----------------------
	function run(){
		$cid	= $this->xml->action->cid;
		$rtype	= $this->xml->action->rtype;
		
		$msg='Spam comment on : http://joind.in/'.$rtype.'/view/'.$cid;
		mail('enygma@phpdeveloper.org','Suggested spam comment!',$msg,'From: info@joind.in');
		
		return array('output'=>'json','items'=>array('msg'=>'Success'));
	}
}