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
    
    protected $_rules = array();
    
    protected $_profile = null;
    
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
        foreach($this->_getExposedData() as $column) {
            
            if(array_key_exists($column, $profileData)) {
                $data[$column] = $profileData[$column];
            }
            
        }
        
        return $data;
    }
    
    /**
     * Returns the column names exposed by this token.
     * @return array
     */
    protected function _getExposedData()
    {
        return array_keys($this->getProfile()->getData());
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