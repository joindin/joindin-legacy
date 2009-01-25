<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Akismet {
	
	var $key	= 'b8bf76a6e0d8';
	var $blog	= 'http://joind.in';

	function send($path,$data){
		$req_str	= '';
		$resp		= '';
		$host		= $this->key.'.rest.akismet.com';
		$port		= 80;
		$data['key']	= $this->key;
		$data['blog']	= $this->blog;
		$data['user_ip']= $_SERVER['REMOTE_ADDR'];
		foreach($data as $k=>$v){ $req_str.=$k.'='.$v.'&'; }
		
		$http ="POST ".$path." HTTP/1.0\r\n";
		$http.="Host: ".$host."\r\n";
		$http.="Content-Type: application/x-www-form-urlencoded;\r\n";
		$http.='Content-length: '.strlen($req_str)."\r\n";
		$http.="User-Agent: Joind.in/1.0\r\n";
		$http.="\r\n";
		$http.=$req_str;
		
		$fp=fsockopen($host,$port,$errno,$errstr,10);
		if($fp){
			fwrite($fp,$http);
			while(!feof($fp)){ $resp.=fgets($fp,1024); }
			fclose($fp);
			$p=explode("\r\n\r\n",$resp);
			return $p[1];
		}else{ return false; }
	}
	
}