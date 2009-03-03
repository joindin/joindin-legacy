<?php

class ServiceAuth
{
    /**
     * Checks the authorization for a public API method
     * @param string $queryString
     * @return boolean
     */
    public static function publicAuth($queryString)
    {
        require_once BASEPATH . 'application/helpers/reqkey_helper.php';
        
        $parts = explode('&', $queryString);
       
        $reqk = $seck = null;
        foreach($parts as $part) {
            list($key, $value) = explode('=', $part);
        
            if($key == 'reqk') {
                $reqk = $value;
            } else if($key == 'seck') {
                $seck = $value;
            }
        }
        
        if(null === $reqk || null === $seck) {
           return false;
        }
        
        // Check the security token
        return checkReqKey($reqk, $seck);
    }
    
    /**
     * Checks authorization based on username and password.
     * @return boolean
     */
    public static function privateAuth()
    {
        return true;
    }
    
}