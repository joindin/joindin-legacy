<?php

/**
 * Base model functionality
 */
abstract class Base
{
	
	public $columns     = array();
    public $values      = array();
    public $table       = null;
    public $orm         = array();
	
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
				$columnNames = array_keys($this->columns);
				
				foreach($columnNames as $column){
					if(strtolower($column) == $getByType){
						// call a get where "col = value"
						$return = $this->find(array($column=>$arguments[0]));

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

    /**
     * Ensure a few things...
     * @param  $inputData
     * @return void
     */
    private function validateData($inputData)
    {
        // be sure that the keys they've given use are allowed
        $allowedKeys = array_keys($this->columns);
        foreach(array_keys($inputData) as $submitKey){
            if(!in_array($submitKey,$allowedKeys)){
                throw new Exception('Column name "'.$submitKey.'" not allowed!');
            }
        }
    }
	
	public function find($where,$filters = null)
	{
        $tableName = $this->table;
        
		$ci = &get_instance();
		$query = $ci->db->get_where($tableName,$where);
		$result = $query->result();

        // see if the class has ORM keys
        if(isset($this->orm) && count($this->orm)>0 && $filters!=null){
            print_r($this->orm);

            // now look at our filters and see which keys to follow
            foreach($filters as $filter){
                if(array_key_exists($filter,$this->orm)){
                    echo 'key exists';
                    // for each of these, use the key to
                }
            }
        }

        return $result;
	}

    /**
     * Given the values, create an new object
     * @param  $inputValues
     * @return void
     */
    public function create($inputValues)
    {
        // ensure that everything's good...
        $this->validateData($inputValues);

        echo '<pre>'; print_r($inputValues); echo '</pre>';
        $ci = &get_instance();
        $result = $ci->db->insert($this->table,$inputValues);
    }
	
}

?>