<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Defensio {
	
	var $key	= 'd22ba53cb84a0555b2d0f3836cfece5c';
	var $http	= 'http://api.defensio.com';
	var $owner	= 'http://joind.in';
	
	function check($name,$comment,$trust,$url){
		$loc='/app/1.2/audit-comment/'.$this->key.'.yaml';
		$arr=array(
			'user-ip'		=>$_SERVER['REMOTE_ADDR'],
			'owner-url'		=>$this->owner,
			'article-date'	=>'',
			'comment-author'=>$name,
			'comment-type'	=>'comment',
			'comment-content'=>$comment,
			'permalink'		=>$owner.$url
		);
		if(isset($_SERVER['HTTP_REFERER'])){
			$arr['referrer']=$_SERVER['HTTP_REFERER'];
		}
		$msg='';
		foreach($arr as $k=>$v){
			$msg.=$k.'='.urlencode($v).'&';
		}
		
		$fp=fsockopen('api.defensio.com',80,$errno,$errstr);
		if($fp){
			$str= "POST ".$loc." HTTP/1.0\r\n";
			$str.="Host: api.defensio.com\r\n";
			$str.="Content-type: application/x-www-form-urlencoded\r\n";
			$str.="Content-length: ".strlen($msg)."\r\n";
			$str.="Connection: close\r\n";
			$str.="\r\n";
			$str.=$msg;
			
			fwrite($str);
			echo 'request: <pre>'.$str.'</pre>';
			
			$resp='';
			while(!feof($fp)){ $resp.=fgets($fp,1024); }
			fclose($fp);
		}
		
		echo 'response: <pre>'.$resp.'</pre>';
		return $resp;
		
	}
	
}