<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class DomainModel
 * @package Core
 * @subpackage Library
 */

/** Model */
require_once BASEPATH . '/libraries/Model.php';
/** DomainModelCache */
require_once BASEPATH . 'application/libraries/DomainModelCache.php';
/** DomainModelValidator */
require_once BASEPATH . 'application/libraries/DomainModelValidator.php';

/**
 * This class allows you to fetch data from the database wrapped 
 * in a model.
 * The model can be used to update the data and save it back to the 
 * database.
 * 
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class DomainModel extends Model
{
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
    protected $_columns = null;
    
    /**
     * Data for this model.
     * @var array
     */
    protected $_data = array();
    
    /**
     * Has many relations with other models.
     * @var array
     */
    protected $_hasMany = array();
    
    /**
     * Has one relations with other models.
     * @var array
     */
    protected $_hasOne = array();
    
    /**
     * Belongs To relations
     * @var array
     */
    protected $_belongsTo = array();

    /**
     * Relations for this model
     * @var array
     */
    protected $_relations = array();

    /**
     * Reference to the validator object. 
     * @var DomainModelValidator
     */
    protected $_validator = null;
    
    /**
     * Rules used when validating this model.
     * @var array
     */
    protected $_rules = array();
    
    /** **/
	
    public function DomainModel($data = null)
    {
        $ci = CI_Base::get_instance();
        // Load the database
        $ci->load->database();
        $this->_database =& $ci->db;
        
        // Discover table information
        $this->_discoverTableMetaData();
        
        // Set the data
        if(!is_null($data) && !is_array($data)) {
            $data = $this->_findBy($this->_primaryKey, $data);
            if(is_null($data) || count($data) === 0) {
                throw new Exception('Model primary key could not be found.');
            }
			$data = array_shift($data);
            $this->setData($data);
        }
        else if(!is_null($data) && is_array($data)) {
            $this->setData($data);
        }

        // Fire post construct hook
        $this->postConstruct();
    }
    
	/**
     * Create a new model with empty or provided data.
     * @param array|string $data
     * @return DomainModel
     */
    public function create($data = null)
    {
        $className = get_class($this);
        
        // If data is null, create a new empty instance ...
        if(null === $data) {
            return new $className;
        }
        
        // else if no identifier value was provide, create a new instance ...
        if(!isset($data[$this->getIdentifierField()]) || empty($data[$this->getIdentifierField()])) {
            return new $className($data);
        }
		
        // else check if the model already exists in the cache
        $identifier = $data[$this->getIdentifierField()];
        if(DomainModelCache::check($className, $identifier)) {
            return DomainModelCache::fetch($className, $identifier);
        } else {
            $model = new $className($data[$this->getIdentifierField()]);
            DomainModelCache::store($model);
            
            return $model;
        }
    }

    /**
     * Delete the model and its data from the database.
     * @return boolean
     */
    public function delete()
    {
        // Fire the pre-delete hook
        $this->preDelete();
        
        $success = false;
        if(!empty($this->_data[$this->_primaryKey])){
            $success = $this->_database->delete($this->_table, array($this->_primaryKey => $this->_data[$this->_primaryKey]));
        }
		
        // Fire the post-delete hook
        $hookReturn = $this->postDelete($success);
        if(!is_null($hookReturn) && is_bool($hookReturn)) {
            $success = $hookReturn;
        }
        
		return $success;
    }
    
    /**
     * Saves the data in the model to the database.
     * @param array $data
     * @return mixed
     */
    public function save($data = null) 
    {
        // Validate
        if(!$this->validate($data)) {
            return false;
        }
        
        $success = false;
        
        // Fire pre-save hook
        $this->preSave();
        
        // Only save data that actually has a column in the database
        $data = array_intersect_key($this->_data, array_flip($this->_columns));
        
        if(empty($this->_data[$this->_primaryKey])) {
            // unset the primary key
            unset($data[$this->_primaryKey]);
            // insert the new data into the database
            $success = $this->_database->insert($this->_table, $data);
            if($success) {
                $this->_data[$this->_primaryKey] = $this->_database->insert_id();
            }
        }
        else {
            // Update the new data to the table
            $primaryKeyValue = $data[$this->_primaryKey];
            unset($data[$this->_primaryKey]);
            $success = $this->_database->update($this->_table, $data, array($this->_primaryKey => $primaryKeyValue));
        }
        
        // Fire the post-dave hook
        $hookReturn = $this->postSave($success);
        if(!is_null($hookReturn) && is_bool($hookReturn)) {
            $success = $hookReturn;
        }
        
        return $success;
    }
    
    /**
     * Sets a validation rule for the specified field.
     * @param string $field
     * @param array|string $rule
     */
    public function setRule($field, $rule) 
    {
        $this->_rules[$field] = $rule;
    }
    
    /**
     * Validates the model data by the validation rules set in $this->_rules.
     * @param array $data
     * @return boolean $valid
     */
    public function validate($data = null)
    {
        // Fire pre-validate hook
        $this->preValidate();
        
    	if(!is_null($data)) {
    		$this->setData($data);
    	}
    	
    	if(null === $this->_validator) {
    		$this->_validator = new DomainModelValidator($this);
    	}

        $success = $this->_validator->run();
    	
    	// Fire post-validate hook
    	$hookReturn = $this->postValidate($success);
        if(!is_null($hookReturn) && is_bool($hookReturn)) {
            $success = $hookReturn;
        }
        
        return $success;
    }
    
    /**
     * @see DomainModel::validate()
     */
    public function isValid($data = null) 
    {
        $this->validate($data);
    }
    
	/**
     * Finds a model by its primary key. This method does not support multiple rows 
     * since a primary key is supposed to be unique. An exception is thrown in case 
     * multiple rows are found.
     * @param string $primaryKey
     * @return DomainModel
     * @throws Exception
     */
    public function find($primaryKey)
    {
        $data = $this->_findBy($this->_primaryKey, $primaryKey);
        
        if(is_null($data)) {
            return null;
        } 
        
        if(!is_array($data) || count($data) > 1) {
            throw new Exception('Multiple rows found for primary key ' . $primaryKey . '.');
        }

        $data = array_shift($data);
        return $this->create($data);
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
    	    // Check the DomainModelCache first ...
    	    if(DomainModelCache::check($className, $rowData[$this->getIdentifierField()])) {
    	        $data[] = DomainModelCache::fetch($className, $rowData[$this->getIdentifierField()]);
    	    }
    	    // if it doesn't exist, create a new instance and cache it
    	    else {
        		$model = new $className((array) $rowData);
        		DomainModelCache::store($model);
        		$data[] = $model;
        	}
    	}
    	
        return $data;
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
     * Sets the data for the model.
     * @param array $data
     * @return DomainModel
     */
    public function setData(array $data)
    {
        foreach($data as $column => $value) {
            $setter = 'set' . $this->_convertToClassMember($column, true);
            $this->$setter($value);
        }
        
        return $this;
    }
    
    /**
     * Set one or more columns. Notice that the database column names need to be used 
     * to set the values unlike the setter methods that uses CamelCased names.
     * @param array $columns
     * @return DomainModel
     */
    public function setColumns(array $columns)
    {
        foreach($columns as $column => $value) {
            $this->_set($column, $value);
        }
        
        return $this;
    }
    
    /**
     * Returns the columns for this model 
     * @return array
     */
    public function getColumns()
    {
        return $this->_columns;
    }
    
    /**
     * Returns the identifier for this model.
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->_data[$this->_primaryKey];
    }
    
    /**
     * Returns the identifier field for this model. By default this is the primary 
     * key column from this models table.
     * @return string
     */
    public function getIdentifierField()
    {
        return $this->_primaryKey;
    }
    
    /**
     * Checks if the model is a new instance by checking the model's identifier.
     * @return boolean
     */
    public function isNew()
    {
        $identifier = $this->getIdentifier();
        return empty($identifier);
    }
    
    /**
     * Returns the validation rules for this model.
     * @return array
     */
    public function getRules()
    {
        return $this->_rules;
    }
    
    /**
     * Returns the errors generated by the validator.
     * @return array|null
     */    
    public function getErrors()
    {
        if(!is_null($this->_validator)) {
            return $this->_validator->getErrors();
        }
        
        return array();
    }
    
    /**
     * Returns the validator for this model.
     * @return DomainModelValidator
     */
    public function getValidator()
    {
        if(null === $this->_validator) {
            $this->_validator = new DomainModelValidator();
        }
        
        return $this->_validator;
    }
    
    /**
     * Returns a list version of all the records for use in dropdowns. The label 
     * is based on a column named 'title', 'label', or 'name' and the key is based 
     * on the primary key column.
     * @return array
     */
    public function getList()
    {
        $key = null;
        if(empty($this->_primaryKey)) {
            // try the id field
            if(in_array('id', $this->_columns)) {
                $key = 'id';
            }
        } else {
           $key = $this->_primaryKey;
        }
        
        if(null == $key) {
            return null;
        }
        
        $label = null;
        if(in_array('name', $this->_columns)) {
            $label = 'name';
        } else if(in_array('title', $this->_columns)) {
            $label = 'title';
        } else if(in_array('label', $this->_columns)) {
            $label = 'label';
        }
        
        if($label == null) {
            return null;
        }
        
        $allData = $this->_findAll();
        
        $list = array();
        foreach($allData as $row) {
            $list[$row[$key]] = $row[$label];
        }
        
        return $list;
    }
    
    /**
     * Logs a message to the central log file. Log message from DomainModels are 
     * prefixed with the child class name.
     * @param string $message
     * @param string $level
     */
    protected function log($message, $level = 'debug')
    {
        $levels = array('error', 'debug', 'info');
        if(!in_array(strtolower($level), $levels)) {
           $level = 'debug';
        }
        // Prefix the class name
        $message = get_class($this) . ': ' . $message;
        
        log_message($level, $message);
    }
    
    /**
     * Find a model by column name. This will return all rows that match the column value.
     * This method will not create model instances, it will only return the raw data.
     * @param string $column
     * @param string $value
     * @return ActiveRecord
     */
    protected function _findBy($column, $value)
    {
        if(!in_array($column, $this->_columns)) {
            throw new Exception("Unknown column \"{$column}\" in table {$this->_table}.");
        }
        
        $where = "`{$column}` = '{$this->_database->escape_str($value)}'";
        $result = $this->_findAll($where);
        
        if(count($result) === 0) {
            return null;
        }
        
        return $result;
    }
    
	/**
     * Find all records for this model. The result can be limited by providing 
     * a where and limit clause and the result can be sorted with an order directive.
     * This method will not create model instances, it will only return the raw data.
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
    	
    	$this->log("query: {$query}");
    	// Execute te query
    	$result = $this->_database->query($query);
    	
        return $result->result_array();
    }
    
    /**
     * Returns the data for the specified column name.
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
     * Returns the relation for the specified label.
     * @param string $label
     * @return mixed
     */
    protected function _getRelation($label)
    {
        if(in_array(strtolower($label), array_map('strtolower', array_keys($this->_relations)))) {
            foreach($this->_relations as $relationLabel => $value) {
                if(strtolower($label) == strtolower($relationLabel)) {
                    return $this->_relations[$relationLabel];
                }
            }
        }
        
        return null;
    }
    
	/**
     * Sets the data for the specified key.
     * @param string $key
     * @param mixed value
     * @return DomainModel
     */
    protected function _set($key, $value)
    {
        if(
            //in_array($column, $this->_columns) && 
            ($key != $this->_primaryKey || empty($this->_data[$this->_primaryKey]))
        ) {
            $this->_data[$key] = $value;
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
     * Fetches related models specified in the $_hasMany array
     */
    protected function _fetchHasManyRelations()
    {
        foreach($this->_hasMany as $label => $options) {
            if(!isset($options['className']) || empty($options['className'])) {
                throw new Exception("No class name specified for relation {$label} in model " . get_class($this) . ".");
            }
            
            $modelName = $options['className'];
            if(!class_exists($modelName)) {
                throw new Exception("Class {$modelName} could was not loaded for relation {$label} in model " . get_class($this) . ".");
            }
            $modelDao = new $modelName;
            
            // Check for the reference column name
            if(!isset($options['referenceColumn']) || empty($options['referenceColumn'])) {
                $referenceColumn = $this->_primaryKey;
            } else if(!empty($options['referenceColumn'])) {
                $referenceColumn = $options['referenceColumn'];
            } else {
                throw new Exception("Error in reference column definition for relation {$label} in model " . get_class($this) . ".");
            }
            
            // Check for the foreign column name
            if(!isset($options['foreignColumn']) || empty($options['foreignColumn'])) {
                $foreignColumn = $modelDao->getIdentifierField();   
            } else if(!empty($options['foreignColumn'])) {
                $foreignColumn = $options['foreignColumn'];
            } else {
                throw new Exception("Error in foreign column definition for relation {$label} in model " . get_class($this) . ".");
            }
            
            // Construct the where claus
            $where = "`{$foreignColumn}` = '{$this->_data[$referenceColumn]}'";
            // Fetch the related models
            $relatedModels = $modelDao->findAll($where);
            
            $label = strtolower($label);
            $this->_relations[$label] = array();
            foreach($relatedModels as $model) {
                $this->_relations[$label][$model->getIdentifier()] = $model;
            }
        }
    }
    
    /**
     * Fetches related models specified by the $_hasOne array
     */
    protected function _fetchHasOneRelations()
    {
        
        foreach($this->_hasOne as $label => $options) {
        
            if(!isset($options['className']) || empty($options['className'])) {
                throw new Exception("No class name specified for relation {$label} in model " . get_class($this) . ".");
            }
            
            $modelName = $options['className'];
            if(!class_exists($modelName)) {
                throw new Exception("Class {$modelName} could was not loaded for relation {$label} in model " . get_class($this) . ".");
            }
            $modelDao = new $modelName;
            
            // Check for the reference column name
            if(!isset($options['referenceColumn']) || empty($options['referenceColumn'])) {
                $referenceColumn = $this->_primaryKey;
            } else if(!empty($options['referenceColumn'])) {
                $referenceColumn = $options['referenceColumn'];
            } else {
                throw new Exception("Error in reference column definition for relation {$label} in model " . get_class($this) . ".");
            }
            
            // Check for the foreign column name
            if(!isset($options['foreignColumn']) || empty($options['foreignColumn'])) {
                $foreignColumn = $modelDao->getIdentifierField();   
            } else if(!empty($options['foreignColumn'])) {
                $foreignColumn = $options['foreignColumn'];
            } else {
                throw new Exception("Error in foreign column definition for relation {$label} in model " . get_class($this) . ".");
            }
            
            $find = "findBy" . $this->_convertToClassMember($foreignColumn, true);
            $childModels = $modelDao->$find($this->_data[$referenceColumn]);
            
            if(count($childModels) === 1) {
                $this->_relations[strtolower($label)] = $childModels[0];
            }
        }
    }
    
    /**
     * Fetches related models specified by the $_belongsTo array
     */
    protected function _fetchBelongsToRelations()
    {
        foreach($this->_belongsTo as $label => $options) {
        
            if(!isset($options['className']) || empty($options['className'])) {
                throw new Exception("No class name specified for relation {$label} in model " . get_class($this) . ".");
            }
            
            $modelName = $options['className'];
            if(!class_exists($modelName)) {
                throw new Exception("Class {$modelName} could was not loaded for relation {$label} in model " . get_class($this) . ".");
            }
            $modelDao = new $modelName;
            
            // Check for the reference column name
            if(!isset($options['referenceColumn']) || empty($options['referenceColumn'])) {
                $referenceColumn = $this->_primaryKey;
            } else if(!empty($options['referenceColumn'])) {
                $referenceColumn = $options['referenceColumn'];
            } else {
                throw new Exception("Error in reference column definition for relation {$label} in model " . get_class($this) . ".");
            }
            
            // Check for the foreign column name
            if(!isset($options['foreignColumn']) || empty($options['foreignColumn'])) {
                $foreignColumn = $modelDao->getIdentifierField();   
            } else if(!empty($options['foreignColumn'])) {
                $foreignColumn = $options['foreignColumn'];
            } else {
                throw new Exception("Error in foreign column definition for relation {$label} in model " . get_class($this) . ".");
            }
            
            $find = "findBy" . $this->_convertToClassMember($foreignColumn, true);
            $parentModels = $modelDao->$find($this->_data[$referenceColumn]);
            
            if(count($parentModels) === 1) {
                $this->_relations[strtolower($label)] = $parentModels[0];
            }
        }
        
    }
    
	/**
     * Catches findBy<Column>, get<Column> and set<Column> method calls. It will check
     * the columns and relations for this model.
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments)
    {
        if(strpos($name, 'findBy') === 0) {
            return $this->_handleFindBy($name, $arguments);
        }
        else if(strpos($name, 'get') === 0) {
            return $this->_handleGet($name);
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
     * Handles a get<column> call.
     * @param string $name
     * @return mixed
     */
    protected function _handleGet($name)
    {
        // Get the correct column name
        $column = $this->_convertToColumnName($name, 'get');

        // Check for data
        $data = $this->_get($column);
        
        // If column doesn't exist, check relations
        if(is_null($data)) {
            
            $relationLabel = str_replace('get', '', $name);
            $data = $this->_getRelation($relationLabel);

            // If data is still NULL, check if a relation was defined so we can fetch it
            if(is_null($data)) {
                // Check if a hasMany relation was specified with the label
                if(in_array(strtolower($relationLabel), array_map('strtolower', array_keys($this->_hasMany)))) {
                    // fetch relations
                    $this->_fetchHasManyRelations();
                    // return
                    $data = $this->_getRelation($relationLabel);
                }
                // Check if a hasOne relation was specified with the label
                else if(in_array(strtolower($relationLabel), array_map('strtolower', array_keys($this->_hasOne)))) {
                    // fetch relations
                    $this->_fetchHasOneRelations();
                    // return
                    $data = $this->_getRelation($relationLabel);
                }
                // Check if a belongsTo relation was specified with the label
                else if(in_array(strtolower($relationLabel), array_map('strtolower', array_keys($this->_belongsTo)))) {
                    // fetch relations
                    $this->_fetchBelongsToRelations();
                    // return
                    $data = $this->_getRelation($relationLabel);
                }
            }
        }

        return $data;
    }
    
    /**
     * Handles a findBy<column> call. This supports multiple rows.
     * @param string $name
     * @return array|null
     */
    protected function _handleFindBy($name, $arguments)
    {
        // Construct the column name
        $column = $this->_convertToColumnName($name, 'findBy');
        
        // Get the data
        $data = $this->_findBy($column, $arguments[0]);
        
        if(is_null($data)) {
            return null;
        }
        
        $models = array();
        foreach($data as $rowData) {
            $models[] = $this->create($rowData);
        }

        // Check for autoshift parameter
        if(isset($arguments[1]) && is_bool($arguments[1])) {
            $models = array_shift($models);
        }
        
        return $models;
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
     * Converts a string to a class member.
     * @param string $value
     */
    protected function _convertToClassMember($value, $allUpperCase = false)
    {
        // Split on underscores
        $parts = split('_', $value);
        
        // Glue the parts together
        $member = '';
        foreach($parts as $part) {
            if(empty($member) && !$allUpperCase) {
                // Only the first part is not uppercased
                $member = $part;
            } else {
                $member .= ucfirst($part);
            }
        }
        
        return $member;
    }
    
    /**
     * Checks if the model has on of the following fields: <br />
     * <ul>
     *  <li>label</li>
     *  <li>title</li>
     *  <li>name</li>
     * </ul>
     * If one of the columns is found (check in that order) this is returned as 
     * the string representation of the model.
     * @return string
     */
    public function __toString()
    {
        if(array_key_exists('label', $this->_data)) {
            return (string) $this->_get('label');
        } else if(array_key_exists('title', $this->_data)) {
            return (string) $this->_get('title');
        } else if(array_key_exists('name', $this->_data)) {
            return (string) $this->_get('name');
        }
        
        return get_class($this) . "({$this->getIdentifier()})";
    }
    
    /**
     * Called after model construction
     */
    protected function postConstruct()
    {}
    
    /**
     * Called before saving the model to the database.
     */
    protected function preSave()
    {}

    /**
     * Called after the attempt is made to save the model to the database. 
     * The outcome of the save attempt is passed to this function.
     * @param boolean $success
     */
    protected function postSave($success)
    {}
    
    /**
     * Called before validating the model.
     */
    protected function preValidate()
    {}

    /**
     * Called after validation of the model is complete. The outcome 
     * of the validation is passed to this function.
     * @param boolean $success
     */
    protected function postValidate($success)
    {}
    
    /**
     * Called before deleting the models data.
     */
    protected function preDelete()
    {}
    
    /** 
     * Called after the models data is deleted from the database. The outcome of 
     * the delete action is passed to this method.
     * @param boolean $success
     */
    protected function postDelete($success)
    {}
}
