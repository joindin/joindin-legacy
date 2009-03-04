<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Profile_model
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';
/** Profile_token_model */
require_once BASEPATH . 'application/models/profile_token_model.php';
/** Country_model */
require_once BASEPATH . 'application/models/country_model.php';

/**
 * Represents a speaker profile.
 * 
 * @author Mattijs Hoitink <mattijs@ibuildings.nl>
 */
class Profile_model extends DomainModel 
{
    protected $_table = 'profiles';
    
    protected $_rules = array(
    	'user_id' => 'required',
    	'full_name' => 'required',
    	'bio' => 'required|strip_tags'
    );
    
    /**
     * Country set for this profile
     * @var Country_model
     */
    protected $_country = null;
    
    /**
     * Find a profile by token
     * @param string $token
     * @return null|Profile_model
     */
    public function findByToken($token) {
        
        $model = new Profile_token_model();
        $profileToken = $model->findByAccessToken($token);
        
        if(is_null($profileToken)) {
            return null;
        }
        
        $profile = new Profile_model($profileToken->getProfileId());
        return $profile;
    }
    
    /**
     * Returns the country set for the profile
     * @param boolean $reload
     * @return string
     */
    public function getCountry($reload = false)
    {
        $this->_fetchCountryModel($reload);
        
        return $this->_country->getName();
        
    }
    
    /**
     * Returns the country model for this profile
     * @param boolean $reload
     * @return Country_model
     */
    public function getCountryModel($reload = false)
    {
        $this->_fetchCountryModel($reload);
        
        return $this->_country;
    }
    
    /**
     * Fetches the country model for this profile
     * @param $reload
     */
    protected function _fetchCountryModel($reload = false)
    {
        if(!is_null($this->_country) && !$reload) {
            return;
        }
        
        $this->_country = new Country_model($this->_data['country_id']);
    }
    
    
    /**
     * Deletes the picture for this profile
     */
    public function deletePicture()
    {
    	if(!empty($this->_data['picture'])) { 
    		if(file_exists(BASEPATH . '..' . $this->_data['picture'])) {
    			unlink(BASEPATH . '..' . $this->_data['picture']);
    		}
    		
    		$this->_data['picture'] = '';
    	}
    }
    
    /**
     * Deletes the profile
     */
    public function delete()
    {
    	// Delete the entry from the database
    	$success = parent::delete();
    	
    	// Delete the picture from the filesystem
    	$this->deletePicture();
    	
    	return $success;
    }
    
}