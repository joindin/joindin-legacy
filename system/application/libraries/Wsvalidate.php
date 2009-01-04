<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Wsvalidate {
	
	// If the check is valid - i.e. the email address is good, return true
	
	var $CI				= null;
	var $default_err 	= 'Missing value: %s';
	var $errs			= array();
	
	function Wsvalidate(){
		$this->CI=&get_instance();
	}
	function validate($rules,$obj){
		$fail=array();
		
		//print_r($rules); print_r($obj);
		foreach($rules as $k=>$v){
			$m=explode('|',$v); //print_r($m);
			//echo $v.' '.$k.' '.$obj->$k."\n";
			foreach($m as $mk=>$mv){
				//$str=(string)$obj->$k;
				//chek to see if we're anything more complex
				if(preg_match('/\[(.*?)\]/',$mv,$matches)){
					//print_r($matches);
					$par	= array_merge(array($k,$obj),explode(',',$matches[1]));
					$func	= str_replace($matches[0],'',$mv);
					$ret 	= call_user_func_array(array(&$this,'validate_'.$func),$par);
				}else{ 
					$ret=$this->{'validate_'.$mv}($k,$obj);
				}
				if(!$ret){
					if($msg=$this->getCustErr($k)){
						$fail[]=$msg;
					}else{ $fail[]=sprintf($this->default_err,$k); }
				}
			}
		}
		return (count($fail)>0) ? $fail : false;
	}
	function setCustErr($k,$msg){
		$this->errs[$k]=$msg;
	}
	function getCustErr($k){
		return (isset($this->errs[$k])) ? $this->errs[$k] : false;
	}
	//---------------
	function validate_required($k,$obj){
		$str=$str=(string)$obj->$k;
		//return (!empty($str)) ? true : false; <-- used this but "false" tripped it
		return (strlen($str)>0) ? true : false;
	}
	function validate_email($k,$obj){
		$str=$str=(string)$obj->$k;
		return (filter_var($str,FILTER_VALIDATE_EMAIL)) ? true : false;
	}
	function validate_date_future($k,$obj){
		$str=$str=(string)$obj->$k;
		return ($str>=time()) ? true : false;
	}
	function validate_int($k,$obj){
		
	}
	function validate_range($k,$obj,$min,$max){
		$this->setCustErr($k,$k.': Number out of range!');
		$num=(float)$obj->$k;
		if(ctype_digit((string)$num)){
			return ($num>=$min && $num<=$max) ? true : false;
		}else{ return false; }
	}
	
	//---------------
	// Multiple field validation - called manually (for now)
	function validate_unique($tbl,$obj){
		$arr=(array)$obj;
		//if there's attributes, unset them
		if(isset($arr['@attributes'])){ unset($arr['@attributes']); }
		
		$mod=$tbl.'_model';
		$this->CI->load->model($mod);
		return $this->CI->$mod->isUnique($arr);
	}
}