<?php

class SSL {
	
	/**
	 * Array listing of secure URLs
	 */
	private $_secure_url	= array(
		"/user/login"
	);
	
	public function __consturct(){
		/* empty */
	}
	
	/**
	 * Main routing function
	 * @param string $path User-defined path
	 */
	public function sslRoute($path=''){
		if(empty($path)){ $path=$_SERVER['REQUEST_URI']; }
		if($this->isSecure($path) && !$this->isRequestSecure()){
			header('Location: '.$this->buildRedirect($path));
		}
	}
	
	/**
	 * Check to see if the given path is secure
	 * @param string $path User-defined path
	 */
	private function isSecure($path){
		return (in_array($path,$this->_secure_url)) ? true : false;
	}
	
	/**
	 * Check to see if the current request is on HTTPS
	 */
	private function isRequestSecure(){
		return ($_SERVER['SECURE']==1) ? true : false;
	}
	
	/**
	 * Make our redirect link to the other side...
	 * @param string $path
	 */
	private function buildRedirect($path=''){
		if(empty($path)){ $path=$_SERVER['REQUEST_URI']; }
		$ci=&get_instance();
		$base_url=$this->ci->config->item('base_url');
		return str_replace('http','https',$base_url).$path;
	}
	
}

?>