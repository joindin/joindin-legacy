<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Service {
	
	private $CI	= null;
	private $_output_types=array('json','xml');
	
	function Service(){
		$this->CI=&get_instance();
	}
	//---------------------
	function handle($type,$data){
		$this->CI->load->model('user_admin_model');
		$data	= trim($data);
		$hdrs	= getallheaders();
		
		// If it's not set, assume it's XML
		if(!isset($hdrs['Content-Type']) || $hdrs['Content-Type']=='text/xml'){
			$xml=$this->parseReqXML($data);
			if(!$xml){ return array('output'=>'msg','data'=>array('msg'=>'Invalid request!')); }
			$rtype	= (string)$xml->action['type'];
		}elseif($hdrs['Content-Type']=='text/json' || $hdrs['Content-Type']=='text/x-json'){
			// We're working with json now...
			$xml	= $this->parseReqJson($data);
			$rtype	= (string)$xml->action['type'];
		}

		/**
		 * So, we want each of the actions to handle their own authentication
		 * information. We need to move the functionality from above (public_actions)
		 * down into the actions themselves
		 *
		 */
		
		ini_set('include_path',ini_get('include_path').PATH_SEPARATOR.BASEPATH.'application/libraries/wsactions');
		$ws_root=$_SERVER['DOCUMENT_ROOT'].'/system/application/libraries/wsactions';

		// Be sure we have at least an action and a type
		if(empty($rtype)){ return array('output'=>'msg','data'=>array('msg'=>'Invalid request type!')); }

		// Get the permissions type of the requested action
		$ret=array();
		$action=$ws_root.'/'.$type.'/'.ucwords($rtype).'.php';
		if(is_file($action)){
			// Get our base web service library
			$this->CI->load->library('wsactions/BaseWsRequest');
			include_once($action);
			$obj=new $rtype($xml);
			
			// Be sure we have a "checkSecurity" method in the object
			if(!method_exists($obj,'checkSecurity')){ 
				return array('output'=>'msg','data'=>array('msg'=>'Internal security error!'));
			}
			
			if($obj && $obj->checkSecurity($xml)){
				// Execute our action...
				$out=$obj->run();
				if(!empty($out)){ $ret=$out; }
				
				//if an output format is specified in the message, use that
				if(isset($xml->action['output'])){ 
					$outf=$xml->action['output'];
					// Be sure it's one of our allowed types
					if(in_array($outf,$this->_output_types)){
						$ret['output']=strtolower($outf); 
					}else{ 
						return array('output'=>'msg','data'=>array('msg'=>'Invalid output type ('.$outf.')!'));
					}
				}
			}else{ 
				return array('output'=>'msg','data'=>array('msg'=>'Invalid permissions!'));
			}
		}else{
			// Invalid request type - error!
			return array('output'=>'msg','data'=>array('msg'=>'Invalid request type ('.$type.'/'.$rtype.')!'));
		}
		return $ret;
		############################

	}
	function parseReqXML($xml){ error_log($xml);
		$ret_xml=null;
		try {
			$ret_xml=simplexml_load_string($xml); //print_r($ret_xml);
		}catch(Exception $e){ /* exceptions */ }
		return $ret_xml;
	}
	// Transform the json into XML and make a SimpleXML object out of it
	function parseReqJson($json){ error_log($json);
		$js=json_decode($json);
		$xml='<request>';
		if(isset($js->request->auth)){
			$xml.='<auth><user>'.$js->request->auth->user.'</user>';
			$xml.='<pass>'.$js->request->auth->pass.'</pass></auth>';
		}
		$xml.='<action type="'.$js->request->action->type.'">';
		foreach($js->request->action->data as $k=>$v){
			$xml.='<'.$k.'>'.$v.'</'.$k.'>';
		}
		$xml.='</action></request>';
		
		//return $js->request;
		return simplexml_load_string($xml);
	}
	function checkAuth($obj){
		$this->CI->load->model('user_model');
		if($obj->auth->user){
			$uinfo=$this->CI->user_model->getUser($obj->auth->user); //echo 'uninfo: '; print_r($uinfo);
			return ($obj->auth->pass==$uinfo[0]->password && $uinfo[0]->api_access) ? true : false;
		}else{ return false; }
	}
	// check to see if our given action is one that doesnt need a user/pass
	function isPublicAction($rtype,$raction){
		$find=$rtype.'/'.$raction; //echo $find;
		return (array_key_exists($find,$this->public_actions)) ? true : false;
	}
	function checkKey(){
		//if it is public, check our "key" they sent along to prevent abuse
		foreach(explode('&',$_SERVER['QUERY_STRING']) as $k=>$v){ 
			$x=explode('=',$v); $_GET[$x[0]]=$x[1]; 
		}
		
		$this->CI->load->helper('reqkey');
		$reqk=$_GET['reqk'];
		$seck=$_GET['seck'];
		return (checkReqKey($reqk,$seck)) ? true : false;
	}
	//------------------------
	function checkPublicRules($rtype,$raction){
		$find=$rtype.'/'.$raction;
		if(array_key_exists($find,$this->public_actions)){
			$pass	= true;
			$rules	= $this->public_actions[$find]; //print_r($rules);
			foreach($rules as $k=>$v){
				$ret=$this->{'rule_'.$v}();
				if(!$ret){ $pass=false; }
			}
			return $pass;
		}else{ return false; }
	}
	function rule_logged(){
		//check to see if they are logged in or not
		return ($this->CI->user_model->isAuth()) ? true : false;
	}
	function rule_isadmin(){
		return ($this->CI->user_model->isSiteAdmin()) ? true : false;
	}	
}
?>