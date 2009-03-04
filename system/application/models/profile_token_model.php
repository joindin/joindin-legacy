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
     * Length of the tokens that are generated
     * @var int
     */
    protected $_tokenLenght = 10;
    
    /**
     * Characters used in token generation.
     * @var string
     */
    protected $_tokenKeySet = 'abcdefghijklm-_ABCDEFGHIJKLMNOPQRSTUVWXYZ-_0123456789-_';
    
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
    
    protected function postSave($success)
    {
        if($success) {
            $this->_saveFields();
        }
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
     * Generates a new token
     * @return string
     */
    public function generate()
    {
		$randkey = '';
		
		for($i = 0; $i < $this->_tokenLenght; $i++) {
			$randkey .= $this->_tokenKeySet[rand(0,(strlen($this->_tokenKeySet)-1))];
		}
		
		return $randkey; 
    }
    
    
    /**
     * Checks if the token is unique
     * @return boolean
     */
    protected function isUnique()
    {
    	return true;
    }
    
}