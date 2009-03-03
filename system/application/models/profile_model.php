<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Profile_model
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';

/** Profile_token_model */
require_once BASEPATH . 'application/models/profile_token_model.php';

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