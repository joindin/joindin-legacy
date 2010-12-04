<?php

/**
 * Base model functionality
 */
abstract class Base
{
	
	public $columns = array();
	
	public function __get($name){
		echo 'get';
	}
	
	public function __call($funcName,$arguments)
	{
		echo 'call: '.$funcName;
		print_r($arguments); echo '<br/><br/>';
		
		$functionName = strtolower($funcName);
		
		if(strpos($functionName,'getby')==0){
			//see if it's a function first....
			if(method_exists($this,$funcName)){
				echo 'method exists';
			}else{
				// doesn't exist - see if we're trying to use one of the columns
				$getByType = str_replace('getby','',$functionName);
				$columnNames = array_Keys($this->columns);
				
				foreach($columnNames as $column){
					if(strtolower($column) == $getByType){
						echo 'type: '.$column;
						
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