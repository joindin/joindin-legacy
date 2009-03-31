<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Profile_token_model
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';
/** Profile_model */
require_once BASEPATH . 'application/models/profile_model.php';

/**
 * Represents an access token to a speaker profile.
 * 
 * @author Mattijs Hoitink <mattijs@ibuildings.nl>
 */
class Profile_token_model extends DomainModel 
{
    protected $_table = 'profile_tokens';
    
    /**
     * Profile for this token
     * @var Profile_model
     */
    protected $_profile = null;
    
    /**
     * Exposed fields for this token
     * @var array
     */
    protected $_fields = array();
    
    /**
     * the minimal length of the generated tokens
     * @var int
     */
    protected $_tokenMinLenght = 10;
    
    /**
     * Characters used in token generation.
     * @var string
     */
    protected $_tokenKeySet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
    /**
     * Caches the list of tokens retrieved from the database
     * @var array
     */
    protected $_tokenCache = null;
    
    /**
     * Returns the profile for this token.
     * @return Profile_model
     */
    public function getProfile()
    {
        if(!is_null($this->_profile)) {
            return $this->_profile;
        }
        
        if(empty($this->_data['profile_id'])) {
            return null;
        }
        
        $this->_profile = new Profile_model($this->_data['profile_id']);
        
        return $this->_profile;
    }
    
    /**
     * Returns the profile data exposed by this token.
     * @return array
     */
    public function getProfileData()
    {
        $profile = $this->getProfile();
        $profileData = $profile->getData();
        
        $data = array();
        foreach($this->getFields() as $column) {
            
            if(array_key_exists($column, $profileData)) {
                $data[$column] = $profileData[$column];
            } else if($column == 'address') {
                $data['street'] = $profileData['street'];
                $data['zip'] = $profileData['zip'];
                $data['city'] = $profileData['city'];
            }
            
        }
        
        return $data;
    }
    
    /**
     * Returns the profile fields exposed by this token
     * @return array
     */
    public function getFields()
    {
        return $this->_fields;
    }
    
    /**
     * Sets the profile fields exposed by this token
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->_fields = $fields;
    }

    /**
     * Saves the fields exposed by this token.
     * This function is a bypass: instead of creating a model we update 
     * database manually.
     */
    protected function _saveFields()
    {
        $tokenId = $this->getId();
        if(empty($tokenId) || !is_numeric($tokenId)) {
            return;
        }
        
        // Start a database transaction
        $this->_database->trans_start();
        // Delete all old fields for this token
        $this->_database->query("DELETE FROM `profile_token_fields` WHERE `profile_token_id` = '{$this->getId()}';");
        foreach($this->_fields as $field) {
            $this->_database->query("INSERT INTO `profile_token_fields` (`profile_token_id`, `field_name`) VALUES ('{$this->getId()}', '{$field}');");
        }
        $this->db->trans_complete(); 
        
    }
    
	/**
     * Collect token fields after construction
     * @see system/application/libraries/DomainModel#postConstruct()
     */
    protected function postConstruct()
    {
        if(!empty($this->_data['id'])) {
            $query = $this->_database->query("SELECT `field_name` FROM `profile_token_fields` WHERE `profile_token_id` = '{$this->_data['id']}'");
            foreach($query->result_array() as $field) {
                $this->_fields[] = $field['field_name'];
            }
        }
    }
    
    /**
     * Save token fields after token is saved properly
     * @see system/application/libraries/DomainModel#postSave()
     */
    protected function postSave($success)
    {
        if($success) {
            $this->_saveFields();
        }
    }
    
    /**
     * Deletes token fields after token has been properly deleted.
     * @see system/application/libraries/DomainModel#postDelete()
     */
    protected function postDelete($success) 
    {
        if($success) {
            $this->_database->trans_start();
            $this->_database->query("DELETE FROM `profile_token_fields` WHERE `profile_token_id` = '{$this->getId()}';");
            $this->db->trans_complete();
        }
    }
    
    /**
     * Returns a newly generated token
     * @return string
     */
    public function generate()
    {
        // Token values
        $tokenString = '';
		$unique = false;
		
		// Start values for the algorithm
        $tokenLength = $this->_tokenMinLenght;
        $currentTry = 0;
        $maxUniqueTries = 10000;
        
        // Generate tokens until we find a unique one
		while(!$unique) {
		    $currentTry++;
		    
		    // Calculate the token lenght to use
		    if($currentTry > $maxUniqueTries) {
		        // Up the token lenght
		        $tokenLength++;
		        // Reset the tries
		        $currentTry = 1;
		    }
		    
		    // Create a new token with the appropriate length
		    $tokenString = $this->_generateToken($tokenLength);
		    
		    // Check if it's unique
		    $unique = $this->isUnique($tokenString);
		}
		
		return $tokenString; 
    }
    
    /**
     * Generates a new token by selecting random characters from $this->_tokenKeySet
     * @param int $length
     * @return string
     */
    private function _generateToken($length = 10)
    {
        $token = '';
        for($i = 0; $i < $length; $i++) {
			$token .= $this->_tokenKeySet[rand(0,(strlen($this->_tokenKeySet)-1))];
		}
		
		return $token;
    }
    
    /**
     * Checks if the token is unique
     * @return boolean
     */
    protected function isUnique($token)
    {
        $token = trim($token);
        if(empty($token)) {
            return false;
        }
        
        // Check the token cache
        if(null === $this->_tokenCache) {
            // Get all the tokens from the database
            $tokens = array();
            $tokenRows = $this->_database->query('SELECT `access_token` FROM `profile_tokens`;');
            foreach($tokenRows->result() as $row) {
                $tokens[] = $row->access_token;
            }
            $this->_tokenCache = $tokens;
        }
        
        // Check if the token exists
        return (!in_array($token, $this->_tokenCache));
    }
    
}