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

        $this->validateTypes($inputData);
    }

    /**
     * Loop through the values given and ensure they match the type
     * NOTE: This method does not yet provide a complete check
     * 
     * @param  $inputData
     * @return void
     */
    private function validateTypes($inputData)
    {
        foreach($inputData as $dataIndex => $data){

            $columnType = $this->columns[$dataIndex];
            preg_match('/(.*?)\((.*?)\)/',$columnType['TYPE'],$matches);

            switch(strtoupper($matches[1])){
                case 'VARCHAR':
                    if(!is_string($data)){
                        throw new Exception('not correct type (string)!');
                    }
                    break;
                case 'INT':
                    if(!ctype_digit($data)){
                        throw new Exception('not correct type (integer)!');
                    }
                    break;
                default:
                    echo 'Error!';
            }

        }
    }
	
	public function find($where,$filters = null,$table = null,$currentObj = null)
	{
        // check the "where" and see if we need to replace
        foreach($where as $key => $value){
            preg_match('/\[(.*?)\]/',$value,$match);
            if(isset($match[1])){
                $where[$key]=str_replace('['.$match[1].']',$currentObj->$match[1],$where[$key]);
            }
        }
        
        $tableName = ($table) ? $table : $this->table;

		$ci = &get_instance();
        $ci->db->select('*');
        $ci->db->from($tableName);
        $ci->db->where($where);
		//$query = $ci->db->get_where($tableName,$where);
        $query      = $ci->db->get();
		$results    = $query->result();

        // see if the class has ORM keys
        if(isset($this->orm) && count($this->orm)>0 && $filters!=null){
            $allowedFilters = array();

            // now look at our filters and see which keys to follow
            foreach($filters as $filter){
                if(array_key_exists($filter,$this->orm)){
                    $allowedFilters[$filter]=$this->orm[$filter];
                }
            }
            
            // loop through the results and get the linked results
            foreach($results as $resultIndex => $result){
                foreach($allowedFilters as $filterName => $filter){
                   $results[$resultIndex]->$filterName = $this->find($filter['key'],null,$filter['table'],$result);
                    
                }
            }
        }
        return $results;
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