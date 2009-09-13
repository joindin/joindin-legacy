<?php
/**
 * Class DomainModelValidator
 * @package Core
 * @subpackage Library
 */

/**
 * Validator class for DomainModels
 * 
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class DomainModelValidator
{

    /**
	 * Prefix used for custom validation callbacks
	 * @var string
	 */
	protected $_callbackPrefix = 'validate_';
	
    /**
     * The DomainModel this validator is connected to.
     * @var DomainModel
     */
    protected $_model = null;
    
    /**
     * The validation rules defined in the model.
     * @var array
     */
    protected $_rules = array();
    
    /**
     * The error messages loaded from the language file.
     * @var array
     */
    protected $_errorMessages = array();
    
    /**
     * The errors that occurred during validation.
     * @var array
     */
    protected $_errors = array();
    
    protected $_allowedTags = '';
    
    /** **/
    
    public function __construct(DomainModel $model)
    {
        $this->_model = $model;
        $this->_rules = $model->getRules();
        $this->_loadErrorMessages();
    }
    
    /**
     * Runs the validator. This will get the latest data from the model and run 
     * that data against the rules defined in the model.
     * @return boolean
     */
    public function run()
    {
        // Get the latest data from the model
        $data = $this->_model->getData();
        
        // Get the columns from the model
        $columns = $this->_model->getColumns();
        
        // reset the errors
    	$this->_errors = array();
        
    	// Loop the fields that need to be validated
    	foreach($this->_rules as $field => $rules) {
    		
    		// Skip non-existing fields
    		if(!in_array($field, $columns)) {
    			continue;
    		}
    		
    		if(is_string($rules)) {
    		    $rules = explode('|', $rules);
    		}
    		
    		// Loop the validation rules
    		foreach($rules as $rule => $param) {
    			// Switch the rule when the array key is an integer
    			if(is_int($rule) || is_numeric($rule)) {
    				$rule = $param;
    				$param = '';
    			}
    			
    			// Check for custom callback
    			if(strpos($rule, $this->_callbackPrefix) === 0) {
    				if(!method_exists($this->_model, $rule)) {
    					throw new Exception(sprintf('Validation callback "%s" does not exist for model ' . get_class($this->_model) . '.', $rule));
    				}
    				
    				$result = $this->_model->$rule($field, $param);
					
					if(!$result) {
						$this->_errors[] = $this->_createErrorMessage($rule, $field);
						continue 2;
					}
    				continue;
    			}
    			
    			// Check if the validation class can handle the rule
    			if(method_exists($this, $rule)) {
    				$result = $this->$rule($data[$field], $param);
    				if(!$result) {
    					$this->_errors[] = $this->_createErrorMessage($rule, $field, $param);
    					continue 2;
    				}
    				continue;
    			}
    			
    			// Check if a native PHP function can handle the rule
    			if(function_exists($rule) && !empty($data[$field])) {
    				$data[$field] = $rule($data[$field]);
    			}
    			
    		}
    		
    	}
    	// Send possible changed data back to the Model
    	$this->_model->setData($data);
    	
    	return (count($this->_errors) === 0);
    }

    /**
     * Returns the errors that occurred during validation.
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }
    
    /**
     * Loads the error message string from a language file.
     */
    protected function _loadErrorMessages()
    {
        $ci = CI_Base::get_instance();
        // Load language file for error messages
        $languageMessages = $ci->lang->load('validation', 'english', true);
        if(is_array($languageMessages)) {
            $this->_errorMessages = array_merge($this->_errorMessages, $languageMessages);
        }
    }
    
    /**
     * Adds an error message for a rule to the validator.
     * @param string $rule
     * @param string $message
     */
    public function addErrorMessage($rule, $message) 
    {
        $this->_errorMessages[$rule] = $message;
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
    		$errorString = sprintf('Validation rule "%s" for field "%s" failed.', $rule, $field);
    	}
    	
    	return $errorString;
    }
    
    /** **/
    
    /**
     * Checks if a value was entered
     * @param string|array $value
     * @return boolean
     */
    public function required($value)
    {
        if(is_array($value)) {
            return (!empty($value));
        }
        else {
            return (trim($value) !== '');
        }
    }
    
    /**
     * Checks if the value matches the value of a given field name.
     * @param string $value
     * @param string $field
     * @return boolean
     */
    public function matches($value, $field)
    {
        $data = $this->_model->getData();
        return ($value == $data[$field]);
    }
    
    /**
     * Checks if the supplied value is a numeric value.
     * @param string $value
     * @return boolean
     */
    public function numeric($value)
    {
        return is_numeric($value);
    }
    
    /**
     * Checks if the supplied value is an integer.
     * @param string $value
     * @return boolean
     */
    public function integer($value)
    {
        return is_int($value);
    }
    
    /**
     * Checks if the supplied value is a valid timestamp.
     * @param string $value
     * @return boolean
     */
    public function timestamp($value) 
    {
        if(empty($value)) {
            return false;
        }
        
        if(!is_numeric($value)) {
            return false;
        }
        
        if(!date('d/m/Y', $value)) {
            return false;
        }
        
        if(!checkdate(date('m', $value), date('d', $value), date('Y', $value))) {
            return false;
        }
    
        return true;
    }
    
    /**
     * Checks if the value is a valid email address
     * @param string $value
     * @return boolean
     */
    public function valid_email($value)
    {
        return (preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $value) > 0);
    }
    
    /**
     * Strips the HTML from the supplied value
     * @param string $value
     * @return string
     */
    public function strip_html($value)
    {
        return strip_tags($value, $this->_allowedTags);
    }
}
