<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Twitter {
    
    var $CI			= null;
    var $api_url	= 'http://search.twitter.com/search.json?q=';
    var $blogin		= 'login';
    var $bkey		= 'key';
    
    function Twitter() {
        $this->CI=&get_instance();
    }
    //---------------------
    public function querySearchAPI($term=null) {
        $this->CI->load->library('cache');
        
        if (!$term || empty($term[0])) { return array(); }
        $ret=array();
        $arr=(!is_array($term)) ? array($term) : $term;
        if (!empty($arr)) {
            foreach ($arr as $k=>$v) {
                $cname='twitter_'.$v;
                // See if we have a file first...
                $ret=$this->CI->cache->getData($cname); //echo '<pre>'; var_dump($ret); echo '</pre>';
                if (!$ret) {
                $url=$this->api_url.str_replace('#','%23', $v); //echo $url;
                $tmp=json_decode(@file_get_contents($url));
                $this->CI->cache->cacheData(trim($cname), $tmp);
                if (!empty($tmp)) { foreach ($tmp as $ok=>$ov) { $ret[]=$ov; } }
                } else {
                $tmp=array();
                foreach ($ret->results as $k=>$v) { $tmp[0][$k]=$v; }
                $ret=$tmp;
                }
            }
        }
        //echo '<!-- <pre>'; print_r($ret); echo '</pre> -->';
        //echo '<pre>'; print_r($ret); echo '</pre>';
        return $ret;
    }
    public function sendMsg($msg, $link=null) {
        $uname	= $this->CI->config->item('twitter_user');
        $pass	= $this->CI->config->item('twitter_pass');
        $out	= '';
        
        $auth	= base64_encode($uname.':'.$pass);
        $content= "status=".$msg;
        
        $out.="POST /statuses/update.xml HTTP/1.1\r\n";
        $out.="Authorization: Basic ".$auth."\r\n";
        $out.="Content-Length: ".strlen($content)."\r\n";
        $out.="Host: twitter.com\r\n";
        $out.="\r\n".$content;
        
        $response='';
        $fp=fsockopen('twitter.com',80, $errno, $errstr);
        if ($fp) {
            fwrite($fp, $out);
            while(!feof($fp)) {
                $response.=fread($fp,1024);
            }
            fclose($fp);
        }
        return $response;
    }
    //---------------------
    public function short_bitly($link) {
        $url='http://api.bit.ly/shorten?version=2.0.1&login='.$this->blogin.'&apiKey='.$this->bkey.'&longUrl='.urlencode($link);
        $ret=json_decode(file_get_contents($url));
        return $ret->results->{"http://joind.in"}->shortUrl;
    }
    
}


?>
