<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Defensio {
	
	var $key	= 'd22ba53cb84a0555b2d0f3836cfece5c';
	var $http	= 'http://api.defensio.com';
	var $owner	= 'http://joind.in';
	
	function check($name,$comment,$trust,$url){
		$resp='';
		$loc='/app/1.2/audit-comment/'.$this->key.'.xml';
		$arr=array(
			'user-ip'		=>$_SERVER['REMOTE_ADDR'],
			'owner-url'		=>$this->owner,
			'article-date'	=>date('Y/m/d'),
			'comment-author'=>$name,
			'comment-type'	=>'comment',
			'comment-content'=>$comment,
			'permalink'		=>$this->owner.$url
		);
		if(isset($_SERVER['HTTP_REFERER'])){
			$arr['referrer']=$_SERVER['HTTP_REFERER'];
		}
		$msg='';
		foreach($arr as $k=>$v){
			$msg.=$k.'='.urlencode($v).'&';
		}
		$str= "POST ".$loc." HTTP/1.0\r\n";
		$str.="Host: api.defensio.com\r\n";
		$str.="Content-type: application/x-www-form-urlencoded\r\n";
		$str.="Content-length: ".strlen($msg)."\r\n";
		$str.="Connection: close\r\n";
		$str.="\r\n";
		$str.=$msg;

		$fp=fsockopen('api.defensio.com',80,$errno,$errstr);
		if($fp){
			fwrite($fp,$str);
			while(!feof($fp)){ $resp.=fread($fp,1024); }
			fclose($fp);
		}
		if($resp){
			$p=explode("\r\n\r\n",$resp);
			$xml=simplexml_load_string($p[1]);
			//echo 'response: <pre>'; print_r($xml); echo '</pre>';
			return $xml;
		}else{ return false; }
	}
	
}