<?php
/**
 * Class SpamBlacklist
 * @package Core
 * @subpackage Library
 */

/**
 * Checks a comment against a spam blacklist.
 *
 * @author Chris Cornut <enygma@phpdeveloper.org>
 */
class SpamBlacklist {
	
	var $source	= 'db';
	var $CI		= null;
	
	// Chk values: regex, substr
	function check($chk, $val, $source='db'){
		$this->CI =& get_instance();
		$pass=true;
		if(is_array($chk)){
			foreach($chk as $k=>$v){
				$ret=$this->{'chk_'.$v}($val);
			}
		}else{ $pass=$this->{'chk_'.$chk}($val); }
		return $pass;
	}
	function fetch_db($type){
		
	}
	function fetch_txt($type='regex'){
		$txt_list=$this->CI->config->item('blacklist_'.$type);
		return file($txt_list);
	}
	//--------------------
	// Check functions
	function chk_substr($val){
		
	}
	function chk_regex($data){
		$pass = true;
		$lines = $this->fetch_txt('regex');
		if($lines != null) {
		    foreach($lines as $key => $value){
			    $match = preg_match($value, $data);
			    if($match) { 
			        return false; 
			    }
		    }
		}
		return true;
	}
	
}
