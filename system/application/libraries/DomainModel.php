<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class DomainModel
 */

/** Model */
require_once BASEPATH . '/libraries/Model.php';

/**
 * ... wrapper for CodeIgniter database. 
 * This class allows you to fetch data from the database wrapped 
 * in a model.
 * 
 * The model can be used to update the data and save it back to the 
 * database.
 * 
 * @author Mattijs Hoitink <mattijs@ibuildings.nl>
 */
class DomainModel extends Model
{
    /**
	 * Prefix used for custom validation callbacks
	 * @var string
	 */
	protected $_callbackPrefix = 'validate_';
	
    /**
     * Reference to the database object
     * @var CI_DB
     */
    protected $_database = null;
    
    /**
     * The name of the table this model is connected to.
     * @var string
     */
    protected $_table = null;
    
    /**
     * Primary key column for the database table.
     * @var string
     */
    protected $_primaryKey = null;
    
    /**
     * Columns in the database table.
     * @var array
     */
    public $_columns = null;
    
    /**
     * Data for this model.
     * @var array
     */
    protected $_data = array();
    
    /**
     * Reference to the validator object. 
     * @var CI_Validation
     */
    protected $_validator = null;
    
    /**
     * Rules used when validating this model.
     * @var array
     */
    protected $_rules = array();
    
    /**
     * Contains model error messages
     * @var array
     */
    protected $_errors = array();
    
	/**
	 * Contains error strings used when errors are displayed.
	 * @var array
	 */
    protected $_errorMessages = array();
    
	
    public function DomainModel($data = null)
    {
        parent::Model();
        // Load the database
        $this->load->database();
        $this->_database =& CI_Base::get_instance()->db;
        // Load validation class
        load_class('Validation');
        
        // Discover table information
        $this->_autodiscoverTableName();
        $this->_discoverTableMetaData();
        
        // Load language file for error messages
        $this->_errorMessages = array_merge($this->_errorMessages, $this->lang->load('validation', 'english', true));
        
        // Set the data
        if(!is_null($data) && !is_array($data)) {
            $data = $this->_findBy($this->_primaryKey, $data);
            if(is_null($data)) {
                throw new Exception('Model primary key could not be found.');
            }
            $this->setData($data);
        }
        else if(!is_null($data) && is_array($data)) {
            $this->setData($data);
        }
    }
    
    /**
     * Finds a model by its primary key
     * @param string $primaryKey
     * @return ActiveRecord
     */
    public function find($primaryKey)
    {
        $className = get_class($this);
        $data = $this->_findBy($this->_primaryKey, $primaryKey);
        return new $className($data);
    }
    
    /**
     * Finds all records for this model and returns them as an array of model instances.
     * @param string|array $where
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function findAll($where = null, $order = null, $limit = null, $offset = null)
    {
        $result = $this->_findAll($where, $order, $limit, $offset);
        $className = get_class($this);
    	
    	$data = array();
    	foreach($result as $rowData) {
    		$data[] = new $className((array) $rowData);
    	}
    	
        return $data;
    }
    
    /**
     * Find all records for this model. The result can be limited by providing 
     * a where and limit clause and the result can be sorted with the order clause.
     * @param string|array $where
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    protected function _findAll($where = null, $order = null, $limit = null, $offset = null) 
    {
    	$query = 'SELECT ' . implode(', ', $this->_columns) . ' FROM ' . $this->_table;
    	
    	// Check where clause
    	if(!is_null($where)) {
    		$whereClause = '';
    		if(is_string($where)) {
    			$whereClause .= ' WHERE ' . $where;
    		} 
    		else if(is_array($where)) {
	    		foreach($where as $column => $value) {
	    			if(empty($whereClause)) {
	    				$whereClause .= " WHERE `{$column}` = '{$value}'";
	    			}
	    			else {
	    				$whereClause .= " AND `{$column}` = '{$value}'";
	    			}
	    		}
    		}
    		$query .= $whereClause;
    	}
    	
    	// Check order clause
    	if(!is_null($order)) {
    		$orderClause = '';
    		preg_match('/^[`]?([A-Za-z][A-Za-z0-9\-_]*)[`]?\s(ASC|DESC)$/', $order, $matches);
    		if(count($matches) == 3) {
    			$orderClause = " ORDER BY `{$matches[1]}` {$matches[2]}";
    		}
    		$query .= $orderClause;
    	}
    	
    	// Check limit clause
    	if(!is_null($limit) && is_numeric($limit)) {
    		$limitClause = ' LIMIT ';
    		if(!is_null($offset) && is_numeric($offset)) {
    			$limitClause .= $offset . ',';
    		}
    		$limitClause .= $limit;
    		$query .= $limitClause;
    	}
    	
    	// Execute te query
    	$result = $this->_database->query($query);
    	
        return $result->result_array();
    }
    
    /**
     * Create a new model with empty or provided data. 
     * @return ActiveRecord
     */
    public function create($data = null)
    {
        $className = get_class($this);
        $newInstance = new $className;
        if(!is_null($data)) {
            $newInstance->setData($data);
        }
        return $newInstance;
    }

    /**
     * Delete the model and its data from the database.
     * @return boolean
     */
    public function delete()
    {
        if(!empty($this->_data[$this->_primaryKey])){
            return $this->_database->delete($this->_table, array($this->_primaryKey => $this->_data[$this->_primaryKey]));
        }
		
		return false;
    }
    
    /**
     * Saves the data in the model to the database.
     * @param array $data
     * @return mixed
     */
    public function save($data = null) 
    {
        if(!is_null($data)) {
            $this->setData($data);
        }
        
        // Validate
        if(!$this->validate()) {
            return false;
        }
        
        if(empty($this->_data[$this->_primaryKey])) {
            // unset the primary key
            unset($this->_data[$this->_primaryKey]);
            // insert the new data into the database
            $success = $this->_database->insert($this->_table, $this->_data);
            if($success) {
                $this->_data[$this->_primaryKey] = $this->_database->insert_id();
            }
            return $success;
        }
        else {
            // Update the new data to the table
            $primaryValue = $this->_data[$this->_primaryKey];
            unset($this->_data[$this->_primaryKey]);
            return $this->_database->update($this->_table, $this->_data, array($this->_primaryKey => $primaryValue));
        }
        
        return false;
    }
    
    /**
     * Validates the model data by the validation rules set in $this->_rules.
     * @param array $data
     * @return boolean $valid
     */
    public function validate($data = null)
    {
    	if(!is_null($data)) {
    		$this->setData($data);
    	}
    	
    	if(null === $this->_validator) {
    		$this->_validator = new CI_Validation();
    	}

        // reset the errors
    	$this->_errors = array();
        
    	// Loop the fields that need to be validated
    	foreach($this->_rules as $field => $rules) {
    		
    		// Skip non-existing fields
    		if(!in_array($field, $this->_columns)) {
    			continue;
    		}
    		
    		if(is_string($rules)) {
    			$rules = array($rules);
    		}
    		
    		// Loop the validation rules
    		foreach($rules as $rule => $param) {
    			// Switch the rule when the array key is an integer
    			if(is_int($rule) || is_numeric($rule)) {
    				$rule = $param;
    				$param = null;
    			} 
    			
    			// Check for required keyword
    			if($rule == 'required') {
    				// Check if the field is set and not empty
    				if(!isset($this->_data[$field]) || empty($this->_data[$field])) {
    					$this->_errors[] = $this->_createErrorMessage('required', $field);
    				}
					continue;
    			}
    			
    			// Check for custom callback
    			if(strpos($rule, $this->_callbackPrefix) === 0) {
    				if(!method_exists($this, $rule)) {
    					throw new Exception(sprintf('Validation callback "%s" does not exist.', $rule));
    				}
    				
    				if(!is_null($param)) {
    					$result = $this->$rule($field, $param);
    				} else {
    					$result = $this->$rule($field);
    				}
					
					if(!$result) {
						$this->_errors[] = $this->_createErrorMessage($rule, $field);
					}
    				continue;
    			}
    			
    			// Check if the validation class can handle the rule
    			if(method_exists($this->_validator, $rule)) {
    				$result = $this->_validator->$rule($field, $param);
    				if(!$result) {
    					$this->_errors[] = $this->_createErrorMessage($rule, $field, $param);
    				}
    				continue;
    			}
    			
    			// Check if a native PHP function can handle the rule
    			if(function_exists($rule) && !empty($this->_data[$field])) {
    				$this->_data[$field] = $rule($this->_data[$field]);
    			}
    			
    		}
    		
    	}
    	
        return (count($this->_errors) === 0);
    }
    
    /**
     * Returns the errors for this model
     * @return array
     */
    public function getErrors()
    {
    	return $this->_errors;
    }
    
    /**
     * Sets the data for the model. Primary key cannot be set and will be 
     * ignored when provided.
     * @param array $data
     */
    public function setData(array $data)
    {
        foreach($data as $column => $value) {
            $this->_set($column, $value);
        }
    }
    
    /**
     * Returns all the data for the model
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }
    
    /**
     * Find a model by column name
     * @param string $column
     * @param string $value
     * @return ActiveRecord
     */
    protected function _findBy($column, $value)
    {
        if(!in_array($column, $this->_columns)) {
            throw new Exception("Unknown column {$column} in table {$this->_table}.");
        }
        
        $statement = $this->_database->get_where($this->_table, array($column => $this->_database->escape_str($value)));
        
        if($statement->num_rows() == 0) {
            return null;
        }
        else if($statement->num_rows() > 1) {
            throw new Exception("Multiple rows found in table \"{$this->_table}\" for key {$value}.");
        }
        
        $data = (array) $statement->row(1);
        
        return $data;
    }
    
    /**
     * Returns the data for column.
     * @param string $column
     * @return mixed
     */
    protected function _get($column)
    {
        if(array_key_exists($column, $this->_data)) {
            return $this->_data[$column];
        }
        
        return null;
    }
    
	/**
     * Sets the data for the specified column. Unknown columns are ignored 
     * and setting the primary key is only allowed once.
     * @param string $column
     * @param mixed value
     * @return ActiveRecord
     */
    protected function _set($column, $value)
    {
        if(
            in_array($column, $this->_columns) && 
            ($column != $this->_primaryKey || empty($this->_data[$this->_primaryKey]))
        ) {
            $this->_data[$column] = $value;
        }
        
        return $this;
    }
    
    /**
     * Returns the table name for this model.
     * @return string
     */
    public function getTableName()
    {
        return $this->_table;
    }
    
    /**
     * Tries to autodiscover the name of the database table for this model.
     * @param boolean $override 
     */
    protected function _autodiscoverTableName($override = false)
    {
        if(is_null($this->_table) || $override) {
            $this->_table = strtolower(get_class($this)) . 's';
        }
    }
    
    /**
     * Tries to discover the table's meta data.
     * @param boolean $override
     */
    protected function _discoverTableMetaData($override = false)
    {
        // Check if table exists in the database
        if(!$this->_database->table_exists($this->_table)) {
            $className = get_class($this);
            throw new Exception("Table \"{$this->_table}\" not found for model \"{$className}\".");
        }
        
        if(is_null($this->_columns) || $override) {
            $this->_columns = array();
            $fieldData = $this->_database->field_data($this->_table);
            foreach($fieldData as $field) {
                $this->_columns[] = $field->name;
                if($field->primary_key == 1) {
                    $this->_primaryKey = $field->name;
                }
            }
            
            // copy the column data to data array
            if(empty($this->_data)) {
                $this->_data = array_fill_keys(array_values($this->_columns), '');
            }
        }
    }
    
	/**
     * Catches findBy<Column>, get<Column> and set<Column> calls.
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments)
    {
        
        if(strpos($name, 'findBy') === 0) {
            
            // Construct the column name
            $column = $this->_convertToColumnName($name, 'findBy');
            
            // Get the data
            $data = $this->_findBy($column, $arguments[0]);
            if(is_null($data)) {
                return null;
            } else {
                return $this->create($data);
            }
            
        }
        else if(strpos($name, 'get') === 0) {
        	$column = $this->_convertToColumnName($name, 'get');
            
            return $this->_get($column);
        }
        else if(strpos($name, 'set') === 0) {
            $column = $this->_convertToColumnName($name, 'set');
            return $this->_set($column, $arguments[0]);
        }
        else {
            throw new Exception("Method {$name} not supported by class " . get_class($this) . ".");
        }
    }
    
    /**
     * Converts a CamelCase word to a column name. The second paramter can be used 
     * to strip a string from the value.
     * @param string $value
     * @param string $strip
     * @return string
     */
    protected function _convertToColumnName($value, $strip = '')
    {
    	if(!empty($strip)) {
    		$value = str_replace($strip, '', $value);
    	}
    	
    	// Split on capitals
        preg_match_all('/[A-Z]{1}[a-z_]*/', $value, $parts);

        // Construct the column name
        $column = implode('_', array_map('strtolower', $parts[0]));
        
        return $column;
    }
    
    /**
     * Creates an error message for a rule and field. 
     * @param string $rule
     * @param string $field
     * @param string $param
     * @return string
     */
    protected function _createErrorMessage($rule, $field, $param = '') 
    {
    	if(array_key_exists($rule, $this->_errorMessages) && !empty($this->_errorMessages[$rule])) {
    		// Construct a cleaner looking field name
    		$fieldName = implode(' ', array_map('ucfirst', explode('_', $field)));
    		
    		$errorString = sprintf($this->_errorMessages[$rule], $fieldName, $param);
    	} 
    	else {
    		$errorString = sprintf('Validation rule %s for field %s failed.', $rule, $field);
    	}
    	
    	return $errorString;
    }
    
    
}
