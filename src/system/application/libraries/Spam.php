<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Spam {
    
    var $source	= 'db';
    var $CI		= null;
    
    // Chk values: regex, substr
    function check($chk, $val, $source='db') { 
        $this->CI =& get_instance();
        $pass=true;
        if (is_array($chk)) {
            foreach ($chk as $k=>$v) {
                $ret=$this->{'chk_'.$v}($val);
            }
        } else { $pass=$this->{'chk_'.$chk}($val); }
        return $pass;
    }
    function fetch_db($type) {
        
    }
    function fetch_txt($type='regex') {
        $txt_list=$this->CI->config->item('blacklist_'.$type);
        return file($txt_list);
    }
    //--------------------
    // Check functions
    function chk_substr($val) {
        
    }
    function chk_regex($val) { 
        $pass=true;
        $rows=$this->fetch_txt('regex'); 
        foreach ($rows as $k=>$v) {
            $m=preg_match($v, $val);
            if ($m) { return false; }
        }
        return true;
    }
    
}
