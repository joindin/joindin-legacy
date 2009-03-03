<?php
function buildReqKey(){
	$CI=&get_instance();
	$reqkey	= '';

	/*
	// our refkey is make up of:
	// - the token (our salt)
	// - the $_SERVER['REQUEST_URI']
	// - a date string - mHdiY
	// - their session id
	// - their IP address
	// all wrapped up in a nice md5
	*/
	$token	= $reqkey=$CI->config->item('token');
	$reqkey.=date('mHdiY');
	$reqkey.=$_SERVER['REQUEST_URI'];
	$reqkey.=$CI->session->userdata('session_id');
	$reqkey.=$CI->session->userdata('ip_address');
	
	return md5($reqkey);
}
function buildSecFile($reqkey){
	$CI = CI_Base::get_instance();
	$skey = mt_rand();
	$dir = $CI->config->item('token_dir');
	$file = $skey . '.tok';
	//make the file with the reqkey value in it
	file_put_contents($dir . '/' . $file, $reqkey);
	
	//do some cleanup - find ones older then the threshold and remove
	$rm = $CI->config->item('token_rm'); //this is in minutes
	if(is_dir($dir)) {
		if($h = opendir($dir)) {
			while(($file = readdir($h)) !== false) {
				if(!in_array($file, array('.', '..'))) {
					$path = $dir . '/' . $file;
					if(filemtime($path) < (time() - ($rm * 60))){ 
					    unlink($path); 
					}
				}
			}
		}
	}
	
	return $skey;
}
function checkReqKey($seckey, $reqkey)
{
	$CI = CI_Base::get_instance();
	$tokenDir = $CI->config->item('token_dir');
	$path = $tokenDir . '/' . $seckey . '.tok';
	if(is_file($path)) {
		$data = file_get_contents($path);
		return ($data == $reqkey) ? true : false;
	}
	else{ 
	    return false; 
	}
}
?>