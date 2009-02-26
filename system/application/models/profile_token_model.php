<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Profile_token_model
 */

/** DomainModel */
require_once BASEPATH . 'application/libraries/DomainModel.php';

/**
 * Represents an access token to a speaker profile.
 * 
 * @author Mattijs Hoitink <mattijs@ibuildings.nl>
 */
class Profile_token_model extends DomainModel 
{
    protected $_table = 'profile_tokens';
    
    protected $_rules = array(
    );
    
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