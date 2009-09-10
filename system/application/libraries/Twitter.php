<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Twitter {
	
	var $CI			= null;
	var $api_url	= 'http://search.twitter.com/search.json?q=';
	
	function Twitter(){
		$this->CI=&get_instance();
	}
	//---------------------
	function querySearchAPI($term=null){
		if(!$term){ return array(); } //echo 'term:'; print_r($term);
		$ret=array();
		$arr=(!is_array($term)) ? array($term) : $term;
		foreach($arr as $k=>$v){
			$url=$this->api_url.str_replace('#','%23',$v); //echo $url;
			$tmp=json_decode(file_get_contents($url));
			foreach($tmp as $ok=>$ov){ $ret[]=$ov; }
		}
		echo '<!-- <pre>'; print_r($ret); echo '</pre> -->';
		return $ret;
	}
	
}


?>
