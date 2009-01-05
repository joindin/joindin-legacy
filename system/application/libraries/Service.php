<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Service {
	
	var $CI	= null;
	
	function Service(){
		$this->CI=&get_instance();
	}
	//---------------------
	function handle($type,$data){
		$this->CI->load->model('user_admin_model');
		$data=trim($data);
		
		$xml=$this->parseReqXML($data);
		if(!$xml){ return array('msg'=>'Invalid request!'); }
		
		//check to be sure they're authed and that they can execute this action type
		if($this->checkAuth($xml)){
			ini_set('include_path',ini_get('include_path').PATH_SEPARATOR.BASEPATH.'application/libraries/wsactions');
			$ws_root=$_SERVER['DOCUMENT_ROOT'].'/system/application/libraries/wsactions/';
			
			//check for execute premissions...
			$uinfo=$this->CI->user_model->getUser($xml->auth->user); //echo 'uninfo: '; print_r($uinfo);
			$uid	= (int)$uinfo[0]->ID;
			$rtype	= (string)$xml->action['type'];
			
			if($this->CI->user_admin_model->hasPerm($uid,0,$rtype)){
				//run our given action	
				//$this->CI->load->library((string)$xml->action['type']);
				$class=ucwords(strtolower((string)$xml->action['type']));
				$class_file=$ws_root.$type.'/'.$class.'.php';
				if(is_file($class_file)){
					include_once($class_file);
					$obj=new $class($xml);
					$ret['data']=$obj->run();
					$ret['output']=$xml->action['output'];
				}else{ $ret=array('msg'=>'Invalid action!'); }
			}else{
				$ret=array('msg'=>'Access denied!');
			}
		}else{ 
			$ret=array('msg'=>'Authentication failed'); 
		}
		return $ret;
	}
	function parseReqXML($xml){
		$ret_xml=null;
		try {
			$ret_xml=simplexml_load_string($xml); //print_r($ret_xml);
		}catch(Exception $e){ }
		return $ret_xml;
	}
	function checkAuth($obj){
		$this->CI->load->model('user_model');
		$uinfo=$this->CI->user_model->getUser($obj->auth->user); //echo 'uninfo: '; print_r($uinfo);
		return (md5($obj->auth->pass)==$uinfo[0]->password && $uinfo[0]->api_access) ? true : false;
	}
}

?>