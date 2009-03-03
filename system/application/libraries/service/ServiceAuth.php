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
    public static function privateAuth($xmlData, $service)
    {
        if(!isset($xmlData->auth) || empty($xmlData->auth)) {
            return false;
        }
        
        $auth = $xmlData->auth;
        $user = (isset($auth->user)) ? $auth->user : null;
        $pass = (isset($auth->pass)) ? $auth->pass : null;
        $action = (isset($xmlData->action['type'])) ? $xmlData->action['type'] : null;
        
        
        if(is_null($user) || is_null($pass) || is_null($action)) {
            return false;
        }
        
        require_once BASEPATH . 'application/models/User_model.php';
        $dao = new User_model();
        $user = $dao->getUser($user);
        
        if(!$user) {
            return false;
        }
        
        if($user->password != $pass) {
            return false;
        }
        
        require_once BASEPATH . 'applcation/models/User_admin_model.php';
        $dao = new User_admin_model();
        $allowed = $dao->hasPerm($user->ID, 0, $action);
        
        
        // All tests passed
        return true;
    }
    
}