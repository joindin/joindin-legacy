<?php
/**
 * Class StringTokenGenerator
 * @package Core
 * @subpackage Library
 */

/**
 * This class generates string tokens that can be used for passwords or access 
 * tokens.
 *
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class StringTokenGenerator
{
    /**
     * Characters used in token generation.
     * @var string
     */
    protected $_tokenKeySet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * List of tokens that will be checked to see if the generated token is unique.
     * @var array
     */
    protected $_tokenReference = array();

    /** **/
    
    public function construct($tokenReference = array()) 
    {
        $this->_tokenReference = $tokenReference;
    }
    
    /**
     * Sets the token reference for the generator.
     * @param array $reference
     */
    public function setTokenReference(array $reference)
    {
        $this->_tokenReference= $reference;
    }
    
    /**
     * Generates a token with the specified length.
     * @param int $length
     * @param array $tokenReference
     * @return string
     */
    public function generate($length = 10) 
    {
        // Token values
        $tokenString = '';
		$unique = false;
		
		// Start values for the algorithm
        $tokenLength = $length;
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
     * Generates a new token based on the token key set.
     * @param int $length
     * @return string
     */
    protected function _generateToken($length) {
        $tokenKeySet = $this->_tokenKeySet;
        
        $token = '';
        for($i = 0; $i < $length; $i++) {
			$token .= $tokenKeySet[mt_rand(0,(strlen($tokenKeySet)-1))];
		}
		
		return $token;
    }
    
    /**
     * Checks if a token is unique by checking the token reference.
     * @param string $token
     * @return boolean
     */
    public function isUnique($token)
    {
        return (!in_array($token, $this->_tokenReference));
    }

}
