<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Twitter {
	
	var $CI			= null;
	var $api_url	= 'http://search.twitter.com/search.json?q=';
	
	function Twitter(){
		$this->CI=&get_instance();
	}
	//---------------------
	function querySearchAPI($term=null){
		$this->CI->load->library('cache');
		
		if(!$term || empty($term[0])){ return array(); }
		$ret=array();
		$arr=(!is_array($term)) ? array($term) : $term;
		if(!empty($arr)){
		    foreach($arr as $k=>$v){
			    $cname='twitter_'.$v;
			    // See if we have a file first...
			    $ret=$this->CI->cache->getData($cname); //echo '<pre>'; var_dump($ret); echo '</pre>';
			    if(!$ret){
				$url=$this->api_url.str_replace('#','%23',$v); //echo $url;
				$tmp=json_decode(@file_get_contents($url));
				$this->CI->cache->cacheData($cname,$tmp);
				if(!empty($tmp)){ foreach($tmp as $ok=>$ov){ $ret[]=$ov; } }
			    }else{
				$tmp=array();
				foreach($ret->results as $k=>$v){ $tmp[0][$k]=$v; }
				$ret=$tmp;
			    }
		    }
		}
		//echo '<!-- <pre>'; print_r($ret); echo '</pre> -->';
		//echo '<pre>'; print_r($ret); echo '</pre>';
		return $ret;
	}
	
}


?>
