<?php

/**
 * Checks service authentication based on a token.
 * 
 * @author Mattijs Hoitink <mattijs@ibuildings.nl>
 *
 */
class ServiceTokenAuth
{
    
    public function checkAuth($xmlData)
    {
        $authData = (isset($xmlData->auth)) ? $xmlData->auth : null;
        
        if(is_null($authData) || empty($authData)) {
            return false;
        }
        
        $token = (string) $authData->token;
        $profileId = (string) $xmlData->action->speaker_id;
        
        $ci = CI_Base::get_instance();
        
        $sql = "SELECT COUNT(`profile_id`) AS `count` FROM `profile_tokens` WHERE `access_token` = ?;";
        try {
            $query = $ci->db->query($sql, array($token));
        } catch(Exception $e) {
            return false;
        }
        
        if(!$query) {
            return false;
        }
        
        if($query->row(1)->count != 1) {
            return false;
        }
        
        // All tests passed
        return true;
    }
    
}