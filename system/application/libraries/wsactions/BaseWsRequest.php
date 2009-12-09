<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
* Base class for web service calls to share methods
*/
class BaseWsRequest {
	
	private $CI	= null;
	
	public function __construct(){
		$this->CI=&get_instance();
	}
	
	public function isValidLogin($xml){
		if(!$this->CI){ $this->CI=&get_instance(); }
		$this->CI->load->model('user_model');
		
		// Check for a valid login
		if(isset($xml->auth->user) && isset($xml->auth->pass)){
			// Check to see if they're a valid user
			if(!$this->CI->user_model->validate($xml->auth->user,$xml->auth->pass,true)){
				// Invalid login! fail!
				return false;
			}else{ return true; }
		}
	}
	
	/**
	* Check our public key, usually used for the Ajax calls on the site
	* to ensure there's no abuse
	*/ 
	public function checkPublicKey(){
		if(!$this->CI){ $this->CI=&get_instance(); }
		
		//if it is public, check our "key" they sent along to prevent abuse
		foreach(explode('&',$_SERVER['QUERY_STRING']) as $k=>$v){ 
			$x=explode('=',$v); $_GET[$x[0]]=$x[1]; 
		}
		
		$this->CI->load->helper('reqkey');
		$reqk=$_GET['reqk'];
		$seck=$_GET['seck'];
		return (checkReqKey($seck,$reqk)) ? true : false;
	}
	
}