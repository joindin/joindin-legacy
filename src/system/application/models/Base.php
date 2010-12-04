<?php

/**
 * Base model functionality
 */
abstract class Base
{
	
	public $columns = array();
	
	public function __get($name){
		$name = strtolower($name);
		return (isset($this->values[$name])) ? $this->values[$name] : null;
	}
	
	public function __call($funcName,$arguments)
	{
		$functionName = strtolower($funcName);
		
		if(strpos($functionName,'getby')==0){
			//see if it's a function first....
			if(method_exists($this,$funcName)){
				echo 'method exists - call that instead';
			}else{
				// doesn't exist - see if we're trying to use one of the columns
				$getByType = str_replace('getby','',$functionName);
				$columnNames = array_Keys($this->columns);
				
				foreach($columnNames as $column){
					if(strtolower($column) == $getByType){
						// call a get where "col = value"
						$return = $this->fetch('events',array($column=>$arguments[0]));

						// apply the values to the object
						foreach($return[0] as $k=>$value){
							if(isset($this->columns[$k])){
								$this->values[$k]=$value;
							}
						}
						
					}
				}
				// $this->columns
			}
		}
	}
	
	public function fetch($tableName,$where)
	{
		$ci = &get_instance();
		$result = $ci->db->get_where($tableName,$where);
		return $result->result();
	}
	
}

?>