<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Akismet {
	
	var $key	= 'b8bf76a6e0d8';

	function send($path,$data){
		$req_str	= '';
		$resp		= '';
		$host		= $this->key.'.rest.akismet.com';
		$port		= 80;
		foreach($data as $k=>$v){ $req_str.=$k.'='.$v.'&'; }
		
		$http ="POST ".$path." HTTP/1.0\r\n";
		$http.="Host: ".$host."\r\n";
		$http.="Content-Type: application/x-www-form-urlencoded;\r\n";
		$http.='Content-length: '.strlen($req_str)."\r\n";
		$http.="User-Agent: Joind.in/1.0\r\n";
		$http.="\r\n";
		$http.=$data;
		
		$fp=fsockopen($host,$port,$errno,$errstr);
		if($fp){
			fwrite($fp,$http);
			while(!feof($fp)){ $resp.=fread($fp,1024); }
			fclose($fp);
			return $resp;
		}else{ return false; }
	}
	
}